<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exercise\StoreExerciseRequest;
use App\Http\Requests\Admin\Exercise\UpdateExerciseRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    /**
     * Получить список всех упражнений
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $exercises = Exercise::with(['equipment'])->withCount('workouts')->get();

        $formattedExercises = $exercises->map(function ($exercise) {
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
                'workouts_count' => $exercise->workouts_count,
                'created_at' => $exercise->created_at?->toISOString(),
                'updated_at' => $exercise->updated_at?->toISOString(),
            ];
        });

        return ApiResponse::data($formattedExercises);
    }

    /**
     * Создать новое упражнение
     *
     * @param StoreExerciseRequest $request
     * @return JsonResponse
     */
    public function store(StoreExerciseRequest $request): JsonResponse
    {
        $exercise = Exercise::create($request->validated());

        $exercise->load('equipment');

        $data = [
            'id' => $exercise->id,
            'title' => $exercise->title,
            'description' => $exercise->description,
            'image' => $exercise->image,
            'muscle_group' => $exercise->muscle_group,
            'equipment' => $exercise->equipment ? [
                'id' => $exercise->equipment->id,
                'name' => $exercise->equipment->name,
            ] : null,
            'workouts_count' => 0,
            'created_at' => $exercise->created_at?->toISOString(),
            'updated_at' => $exercise->updated_at?->toISOString(),
        ];

        return ApiResponse::success('Упражнение успешно создано', $data, 201);
    }

    /**
     * Получить упражнение по ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $exercise = Exercise::with(['equipment', 'workouts'])->withCount('workouts')->find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        $formattedWorkouts = $exercise->workouts->map(function ($workout) {
            return [
                'id' => $workout->id,
                'title' => $workout->title,
                'description' => $workout->description,
                'duration_minutes' => $workout->duration_minutes,
                'is_active' => $workout->is_active,
                'pivot' => [
                    'sets' => $workout->pivot->sets,
                    'reps' => $workout->pivot->reps,
                    'order_number' => $workout->pivot->order_number,
                ],
            ];
        });

        $data = [
            'id' => $exercise->id,
            'title' => $exercise->title,
            'description' => $exercise->description,
            'image' => $exercise->image,
            'muscle_group' => $exercise->muscle_group,
            'equipment' => $exercise->equipment ? [
                'id' => $exercise->equipment->id,
                'name' => $exercise->equipment->name,
            ] : null,
            'workouts_count' => $exercise->workouts_count,
            'workouts' => $formattedWorkouts,
            'created_at' => $exercise->created_at?->toISOString(),
            'updated_at' => $exercise->updated_at?->toISOString(),
        ];

        return ApiResponse::data($data);
    }

    /**
     * Обновить упражнение
     *
     * @param UpdateExerciseRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateExerciseRequest $request, int $id): JsonResponse
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        $exercise->update($request->validated());
        $exercise->load('equipment');

        return ApiResponse::success('Упражнение успешно обновлено', $exercise);
    }

    /**
     * Удалить упражнение
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        if ($exercise->workouts()->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить упражнение, которое используется в тренировках',
                422
            );
        }

        if ($exercise->testResults()->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить упражнение, для которого есть результаты тестов',
                422
            );
        }

        $exercise->delete();

        return ApiResponse::success('Упражнение успешно удалено');
    }
}
