<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Workout\FilterWorkoutRequest;
use App\Http\Requests\Admin\Workout\StoreWorkoutRequest;
use App\Http\Requests\Admin\Workout\UpdateWorkoutRequest;
use App\Http\Requests\Admin\Workout\UploadWorkoutImageRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WorkoutController extends Controller
{
    public function index(FilterWorkoutRequest $request): JsonResponse
    {
        $query = Workout::with(['phase'])
            ->withCount(['exercises', 'warmups', 'userWorkouts']);

        if ($request->filled('search')) {
            $query->search($request->search, ['title', 'description']);
        }

        $workouts = $query->paginate($request->getPerPage());

        $formattedWorkouts = collect($workouts->items())->map(function ($workout) {
            return [
                'id' => $workout->id,
                'title' => $workout->title,
                'description' => $workout->description,
                'duration_minutes' => $workout->duration_minutes,
                'image' => $workout->image,
                'image_url' => $workout->image_url,
                'is_active' => $workout->is_active,
                'phase' => $workout->phase ? [
                    'id' => $workout->phase->id,
                    'name' => $workout->phase->name,
                ] : null,
                'exercises_count' => $workout->exercises_count,
                'warmups_count' => $workout->warmups_count,
                'user_workouts_count' => $workout->user_workouts_count,
                'created_at' => $workout->created_at?->toISOString(),
                'updated_at' => $workout->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $formattedWorkouts,
            'meta' => [
                'current_page' => $workouts->currentPage(),
                'last_page' => $workouts->lastPage(),
                'per_page' => $workouts->perPage(),
                'total' => $workouts->total(),
                'from' => $workouts->firstItem(),
                'to' => $workouts->lastItem(),
            ],
        ]);
    }

    public function store(StoreWorkoutRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->except(['exercises', 'warmups']);

            $workout = Workout::create($data);

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

        return ApiResponse::success('success', $this->formatWorkout($workout));
    }

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
            $data = $request->except(['image', 'exercises', 'warmups']);

            if ($request->hasFile('image')) {
                if ($workout->image) {
                    Storage::disk('public')->delete($workout->image);
                }

                $path = $request->file('image')->store('workouts', 'public');
                $data['image'] = $path;
            }

            $workout->update($data);

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

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обновлении тренировки: ' . $e->getMessage(),
                500
            );
        }
    }

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
                409
            );
        }

        DB::beginTransaction();

        try {
            if ($workout->image) {
                Storage::disk('public')->delete($workout->image);
            }

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

    private function formatWorkout(Workout $workout): array
    {
        $formattedExercises = $workout->exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'title' => $exercise->title,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'image_url' => $exercise->image_url,
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
                'image_url' => $warmup->image_url,
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
            'image' => $workout->image,
            'image_url' => $workout->image_url,
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

    public function uploadImage(UploadWorkoutImageRequest $request, int $id): JsonResponse
    {
        $workout = Workout::find($id);

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        try {
            if ($workout->image) {
                Storage::disk('public')->delete($workout->image);
            }

            $path = $request->file('image')->store('workouts', 'public');
            $workout->update(['image' => $path]);

            return ApiResponse::success('Изображение успешно загружено', [
                'image' => $workout->image,
                'image_url' => $workout->image_url,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при загрузке изображения: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getImage(int $id)
    {
        $workout = Workout::find($id);

        if (!$workout || !$workout->image) {
            return $this->getDefaultImage();
        }

        $path = Storage::disk('public')->path($workout->image);

        if (empty($workout->image) || !file_exists($path)) {
            return $this->getDefaultImage();
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=86400'
        ]);
    }

    private function getDefaultImage()
    {
        $defaultPath = public_path('images/default-workout.png');

        if (file_exists($defaultPath)) {
            return response()->file($defaultPath, [
                'Content-Type' => mime_content_type($defaultPath),
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        return response()->json([
            'code' => ErrorResponse::NOT_FOUND,
            'message' => 'Изображение не найдено'
        ], 404);
    }
}
