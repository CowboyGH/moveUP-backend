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
        $query = TestingExercise::query()
            ->withCount('testings');

        // Поиск по названию и описанию
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Пагинация
        $exercises = $query->paginate($request->getPerPage());

        $formattedExercises = collect($exercises->items())->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'title' => $exercise->title,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'image_url' => $exercise->image_url,
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
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testing-exercises', 'public');
            $data['image'] = $path;
        }

        $exercise = TestingExercise::create($data);
        $exercise->refresh();

        $responseData = [
            'id' => $exercise->id,
            'title' => $exercise->title,
            'description' => $exercise->description,
            'image' => $exercise->image,
            'image_url' => $exercise->image_url,
            'created_at' => $exercise->created_at,
            'updated_at' => $exercise->updated_at,
            'testings_count' => 0,
        ];

        return ApiResponse::success('Тестовое упражнение успешно создано', $responseData, 201);
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

        $data = $request->only(['title', 'description']);
        $exercise->update($data);
        $exercise->refresh();

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

        // Удаляем изображение
        if ($exercise->image) {
            Storage::disk('public')->delete($exercise->image);
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

        if ($exercise->image) {
            Storage::disk('public')->delete($exercise->image);
        }

        $path = $request->file('image')->store('testing-exercises', 'public');
        $exercise->update(['image' => $path]);
        $exercise->refresh();

        return ApiResponse::success('Изображение тестового упражнения обновлено', $exercise);
    }
}
