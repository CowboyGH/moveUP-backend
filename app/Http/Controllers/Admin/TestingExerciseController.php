<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestingExercise\StoreTestingExerciseRequest;
use App\Http\Requests\Admin\TestingExercise\UpdateTestingExerciseRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestingExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $exercises = TestingExercise::withCount('testings')->get();
        return ApiResponse::data($exercises);
    }
    public function store(StoreTestingExerciseRequest $request): JsonResponse
    {
        $exercise = TestingExercise::create([
            'description' => $request->description,
            'image' => $request->image,
        ]);

        $data = [
            'id' => $exercise->id,
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
        $exercise->update($request->only(['description', 'image']));
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
