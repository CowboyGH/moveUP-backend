<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exercise\FilterExerciseRequest;
use App\Http\Requests\Admin\Exercise\StoreExerciseRequest;
use App\Http\Requests\Admin\Exercise\UpdateExerciseRequest;
use App\Http\Requests\Admin\Exercise\UploadExerciseImageRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    public function index(FilterExerciseRequest $request): JsonResponse
    {
        $query = Exercise::with(['equipment'])->withCount('workouts');

        if ($request->filled('search')) {
            $query->search($request->search, ['title', 'description', 'muscle_group']);
        }

        $exercises = $query->paginate($request->getPerPage());

        $formattedExercises = collect($exercises->items())->map(function ($exercise) {
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
                'workouts_count' => $exercise->workouts_count,
                'created_at' => $exercise->created_at?->toISOString(),
                'updated_at' => $exercise->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $formattedExercises,
            'meta' => [
                'current_page' => $exercises->currentPage(),
                'last_page' => $exercises->lastPage(),
                'per_page' => $exercises->perPage(),
                'total' => $exercises->total(),
                'from' => $exercises->firstItem(),
                'to' => $exercises->lastItem(),
            ],
        ]);
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {
        try {
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('exercises', 'public');
                $data['image'] = $path;
            }

            $exercise = Exercise::create($data);
            $exercise->load('equipment');

            $responseData = [
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
                'workouts_count' => 0,
                'created_at' => $exercise->created_at?->toISOString(),
                'updated_at' => $exercise->updated_at?->toISOString(),
            ];

            return ApiResponse::success('Упражнение успешно создано', $responseData, 201);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при создании упражнения: ' . $e->getMessage(),
                500
            );
        }
    }

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
                'image_url' => $workout->image_url,
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
            'image_url' => $exercise->image_url,
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

        return ApiResponse::success('success', $data);
    }

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

        try {
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                if ($exercise->image) {
                    Storage::disk('public')->delete($exercise->image);
                }

                $path = $request->file('image')->store('exercises', 'public');
                $data['image'] = $path;
            }

            $exercise->update($data);
            $exercise->load('equipment');

            return ApiResponse::success('Упражнение успешно обновлено', [
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
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обновлении упражнения: ' . $e->getMessage(),
                500
            );
        }
    }

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
                409
            );
        }

        try {
            if ($exercise->image) {
                Storage::disk('public')->delete($exercise->image);
            }

            $exercise->delete();

            return ApiResponse::success('Упражнение успешно удалено');

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при удалении упражнения: ' . $e->getMessage(),
                500
            );
        }
    }

    public function uploadImage(UploadExerciseImageRequest $request, int $id): JsonResponse
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        try {
            if ($exercise->image) {
                Storage::disk('public')->delete($exercise->image);
            }

            $path = $request->file('image')->store('exercises', 'public');
            $exercise->update(['image' => $path]);

            return ApiResponse::success('Изображение успешно загружено', [
                'image' => $exercise->image,
                'image_url' => $exercise->image_url,
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
        $exercise = Exercise::find($id);

        if (!$exercise || !$exercise->image) {
            return $this->getDefaultImage();
        }

        $path = Storage::disk('public')->path($exercise->image);

        if (empty($exercise->image) || !file_exists($path)) {
            return $this->getDefaultImage();
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=86400'
        ]);
    }

    private function getDefaultImage()
    {
        $defaultPath = public_path('images/default-exercise.png');

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
