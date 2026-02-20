<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Testing\StoreTestingRequest;
use App\Http\Requests\Admin\Testing\UpdateTestingRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Testing;
use App\Models\TestingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestingController extends Controller
{
    public function index(): JsonResponse
    {
        $testings = Testing::with(['categories', 'testExercises'])->withCount('testResults')->get();
        return ApiResponse::data($testings);
    }

    public function store(StoreTestingRequest $request): JsonResponse
    {
        $testing = Testing::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'image' => $request->image,
            'is_active' => $request->is_active ?? true,
        ]);
        if ($request->has('category_ids')) {
            $testing->categories()->sync($request->category_ids);
        }
        if ($request->has('exercise_ids')) {
            $exercisesWithOrder = [];
            foreach ($request->exercise_ids as $order => $exerciseId) {
                $exercisesWithOrder[$exerciseId] = ['order_number' => $order];
            }
            $testing->testExercises()->sync($exercisesWithOrder);
        }
        $testing->load(['categories', 'testExercises']);
        $data = [
            'id' => $testing->id,
            'title' => $testing->title,
            'description' => $testing->description,
            'duration_minutes' => $testing->duration_minutes,
            'image' => $testing->image,
            'is_active' => $testing->is_active,
            'categories' => $testing->categories,
            'exercises' => $testing->testExercises,
            'created_at' => $testing->created_at,
            'updated_at' => $testing->updated_at,
        ];
        return ApiResponse::success('Тест успешно создан', $data,201);
    }

    public function show(int $id): JsonResponse
    {
        $testing = Testing::with(['categories', 'testExercises', 'testResults.user'])->withCount('testResults')->find($id);
        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }
        return ApiResponse::data($testing);
    }

    public function update(UpdateTestingRequest $request, int $id): JsonResponse
    {
        $testing = Testing::find($id);
        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }
        try {
            DB::beginTransaction();

            $testing->update($request->only([
                'title',
                'description',
                'duration_minutes',
                'image',
                'is_active'
            ]));
            if ($request->has('category_ids')) {
                $testing->categories()->sync($request->category_ids);
            }
            if ($request->has('exercise_ids')) {
                $exercisesWithOrder = [];
                foreach ($request->exercise_ids as $order => $exerciseId) {
                    $exercisesWithOrder[$exerciseId] = ['order_number' => $order];
                }
                $testing->testExercises()->sync($exercisesWithOrder);
            }
            DB::commit();

            $testing->load(['categories', 'testExercises']);

            return ApiResponse::success('Тест успешно обновлен', $testing);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обновлении теста: ' . $e->getMessage(),
                500
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $testing = Testing::find($id);

        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }
        $testing->delete();
        return ApiResponse::success('Тест успешно удален');
    }

    public function toggleActive(int $id): JsonResponse
    {
        $testing = Testing::find($id);
        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }
        $testing->is_active = !$testing->is_active;
        $testing->save();
        $status = $testing->is_active ? 'активирован' : 'деактивирован';
        return ApiResponse::success("Тест успешно {$status}", $testing);
    }
}
