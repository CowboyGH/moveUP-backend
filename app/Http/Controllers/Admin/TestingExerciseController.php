<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestingExercise\FilterTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\StoreTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\UpdateTestingExerciseRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestingExerciseController extends Controller
{
    public function index(FilterTestingExerciseRequest $request): JsonResponse
    {
        $query = TestingExercise::with('exercise')
            ->withCount('testings');

        // Поиск по описанию
        if ($request->filled('search')) {
            $query->search($request->search, ['description']);
        }

        // Фильтр по ID упражнения
        if ($request->filled('exercise_id')) {
            $query->where('exercise_id', $request->exercise_id);
        }

        // Фильтр по наличию в тестах
        if ($request->filled('has_testings')) {
            if ($request->has_testings) {
                $query->has('testings');
            } else {
                $query->doesntHave('testings');
            }
        }

        // Фильтр по датам
        $query->dateFilter($request->date_from, $request->date_to);

        // Сортировка
        $query->orderBy($request->getSortBy(), $request->getSortDir());

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
        $exercise = TestingExercise::create([
            'exercise_id' => $request->exercise_id,
            'description' => $request->description,
            'image' => $request->image,
        ]);

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
        return ApiResponse::data($exercise);
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
                422
            );
        }
        $exercise->delete();
        return ApiResponse::success('Тестовое упражнение успешно удалено');
    }
}
