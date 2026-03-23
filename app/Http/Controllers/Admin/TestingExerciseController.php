<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestingExercise\FilterTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\StoreTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\UpdateTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\UpdateTestingExerciseImageRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TestingExerciseController extends Controller
{
    public function index(FilterTestingExerciseRequest $request): JsonResponse
    {
        $query = TestingExercise::with('exercise')
            ->withCount('testings');

        // Только поиск по описанию
        if ($request->filled('search')) {
            $query->search($request->search, ['description']);
        }

        // Пагинация
        $exercises = $query->paginate($request->getPerPage());

        $formattedExercises = collect($exercises->items())->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'exercise_id' => $exercise->exercise_id,
                'exercise' => $exercise->exercise ? [
                    'id' => $exercise->exercise->id,
                    'title' => $exercise->exercise->title,
                ] : null,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'testings_count' => $exercise->testings_count,
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

    public function store(StoreTestingExerciseRequest $request): JsonResponse
    {
        $data = [
            'exercise_id' => $request->exercise_id,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testing-exercises', 'public');
            $data['image'] = $path;
        }

        $exercise = TestingExercise::create($data);
        $data = [
            'id' => $exercise->id,
            'exercise_id' => $exercise->exercise_id,
            'description' => $exercise->description,
            'image' => $exercise->image,
            'created_at' => $exercise->created_at,
            'updated_at' => $exercise->updated_at,
            'testings_count' => 0,
        ];
        return ApiResponse::success('Тестовое упражнение успешно создано', $data, 201);
    }

    public function show(int $id): JsonResponse
    {
        $exercise = TestingExercise::with('testings')->find($id);
        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тестовое упражнение не найдено',
                404
            );
        }
        // Используем success с сообщением вместо data
        return ApiResponse::success('success', $exercise);
    }

    public function update(UpdateTestingExerciseRequest $request, int $id): JsonResponse
    {
        $exercise = TestingExercise::find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тестовое упражнение не найдено',
                404
            );
        }
        $exercise->update($request->only(['exercise_id', 'description', 'image']));
        return ApiResponse::success('Тестовое упражнение успешно обновлено', $exercise);
    }

    public function destroy(int $id): JsonResponse
    {
        $exercise = TestingExercise::find($id);
        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тестовое упражнение не найдено',
                404
            );
        }
        if ($exercise->testings()->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить упражнение, которое используется в тестах',
                409
            );
        }
        $exercise->delete();
        return ApiResponse::success('Тестовое упражнение успешно удалено');
    }

    public function updateImage(UpdateTestingExerciseImageRequest $request, int $id): JsonResponse
    {
        $exercise = TestingExercise::find($id);

        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тестовое упражнение не найдено',
                404
            );
        }

        if ($exercise->getRawOriginal('image')) {
            Storage::disk('public')->delete($exercise->getRawOriginal('image'));
        }

        $path = $request->file('image')->store('testing-exercises', 'public');
        $exercise->update(['image' => $path]);
        $exercise->load('exercise');

        return ApiResponse::success('Изображение тестового упражнения обновлено', $exercise);
    }
}
