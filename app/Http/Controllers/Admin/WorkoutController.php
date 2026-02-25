<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Workout\StoreWorkoutRequest;
use App\Http\Requests\Admin\Workout\UpdateWorkoutRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutWarmup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WorkoutController extends Controller
{
    /**
     * Получить список всех тренировок
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $workouts = Workout::with(['phase', 'exercises', 'warmups'])
            ->withCount(['userWorkouts'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'duration_minutes' => $workout->duration_minutes,
                    'is_active' => $workout->is_active,
                    'phase' => $workout->phase ? [
                        'id' => $workout->phase->id,
                        'name' => $workout->phase->name,
                    ] : null,
                    'exercises_count' => $workout->exercises->count(),
                    'warmups_count' => $workout->warmups->count(),
                    'user_workouts_count' => $workout->user_workouts_count,
                    'created_at' => $workout->created_at?->toISOString(),
                    'updated_at' => $workout->updated_at?->toISOString(),
                ];
            });

        return ApiResponse::data($workouts);
    }

    /**
     * Создать новую тренировку
     *
     * @param StoreWorkoutRequest $request
     * @return JsonResponse
     */
    public function store(StoreWorkoutRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $workout = Workout::create([
                'phase_id' => $request->phase_id,
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'is_active' => $request->is_active ?? true,
            ]);

            if ($request->has('exercises')) {
                foreach ($request->exercises as $exercise) {
                    $workout->exercises()->attach($exercise['exercise_id'], [
                        'sets' => $exercise['sets'],
                        'reps' => $exercise['reps'],
                        'order_number' => $exercise['order_number'],
                    ]);
                }
            }

            if ($request->has('warmups')) {
                foreach ($request->warmups as $warmup) {
                    $workout->warmups()->attach($warmup['warmup_id'], [
                        'order_number' => $warmup['order_number'],
                    ]);
                }
            }

            DB::commit();

            $workout->load(['phase', 'exercises', 'warmups']);

            return ApiResponse::success('Тренировка успешно создана', $this->formatWorkout($workout), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при создании тренировки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Получить тренировку по ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $workout = Workout::with(['phase', 'exercises.equipment', 'warmups'])->find($id);

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        return ApiResponse::data($this->formatWorkout($workout));
    }

    /**
     * Обновить тренировку
     *
     * @param UpdateWorkoutRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateWorkoutRequest $request, int $id): JsonResponse
    {
        $workout = Workout::find($id);

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        DB::beginTransaction();

        try {
            $workout->update($request->only([
                'phase_id',
                'title',
                'description',
                'duration_minutes',
                'is_active'
            ]));

            if ($request->has('exercises')) {
                $workout->exercises()->detach();
                foreach ($request->exercises as $exercise) {
                    $workout->exercises()->attach($exercise['exercise_id'], [
                        'sets' => $exercise['sets'],
                        'reps' => $exercise['reps'],
                        'order_number' => $exercise['order_number'],
                    ]);
                }
            }

            if ($request->has('warmups')) {
                $workout->warmups()->detach();
                foreach ($request->warmups as $warmup) {
                    $workout->warmups()->attach($warmup['warmup_id'], [
                        'order_number' => $warmup['order_number'],
                    ]);
                }
            }

            DB::commit();

            $workout->load(['phase', 'exercises', 'warmups']);

            return ApiResponse::success('Тренировка успешно обновлена', $this->formatWorkout($workout));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обновлении тренировки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Удалить тренировку
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $workout = Workout::find($id);

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        if ($workout->userWorkouts()->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить тренировку, которая уже была назначена пользователям',
                422
            );
        }

        DB::beginTransaction();

        try {
            $workout->exercises()->detach();
            $workout->warmups()->detach();
            $workout->delete();

            DB::commit();

            return ApiResponse::success('Тренировка успешно удалена');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при удалении тренировки: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Форматирование данных тренировки для ответа
     *
     * @param Workout $workout
     * @return array
     */
    private function formatWorkout(Workout $workout): array
    {
        $formattedExercises = $workout->exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'title' => $exercise->title,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'muscle_group' => $exercise->muscle_group,
                'equipment' => $exercise->equipment ? [
                    'id' => $exercise->equipment->id,
                    'name' => $exercise->equipment->name,
                ] : null,
                'pivot' => [
                    'sets' => $exercise->pivot->sets,
                    'reps' => $exercise->pivot->reps,
                    'order_number' => $exercise->pivot->order_number,
                ],
            ];
        })->sortBy('pivot.order_number')->values();

        $formattedWarmups = $workout->warmups->map(function ($warmup) {
            return [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'pivot' => [
                    'order_number' => $warmup->pivot->order_number,
                ],
            ];
        })->sortBy('pivot.order_number')->values();

        return [
            'id' => $workout->id,
            'title' => $workout->title,
            'description' => $workout->description,
            'duration_minutes' => $workout->duration_minutes,
            'is_active' => $workout->is_active,
            'phase' => $workout->phase ? [
                'id' => $workout->phase->id,
                'name' => $workout->phase->name,
            ] : null,
            'exercises' => $formattedExercises,
            'warmups' => $formattedWarmups,
            'user_workouts_count' => $workout->userWorkouts()->count(),
            'created_at' => $workout->created_at?->toISOString(),
            'updated_at' => $workout->updated_at?->toISOString(),
        ];
    }
}
