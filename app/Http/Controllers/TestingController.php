<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Testing;
use App\Models\TestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TestingController extends Controller
{
    public function index(): JsonResponse
    {
        $testings = Testing::where('is_active', 1)
            ->with(['categories', 'testExercises'])
            ->get()
            ->map(function ($testing) {
                return [
                    'id' => $testing->id,
                    'title' => $testing->title,
                    'description' => $testing->description,
                    'duration_minutes' => $testing->duration_minutes,
                    'image' => $testing->image,
                    'categories' => $testing->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                    'exercises_count' => $testing->testExercises->count(),
                ];
            });

        return ApiResponse::data($testings);
    }

    public function show(int $id): JsonResponse
    {
        $testing = Testing::where('id', $id)->where('is_active', 1)->with(['testExercises', 'categories'])->first();

        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }

        $formattedExercises = $testing->testExercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'order_number' => $exercise->pivot->order_number,
            ];
        })->sortBy('order_number')->values();

        $data = [
            'id' => $testing->id,
            'title' => $testing->title,
            'description' => $testing->description,
            'duration_minutes' => $testing->duration_minutes,
            'image' => $testing->image,
            'categories' => $testing->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
            'exercises' => $formattedExercises,
        ];

        return ApiResponse::data($data);
    }

    public function myTestHistory(): JsonResponse
    {
        $user = auth()->user();

        $testResults = TestResult::with(['testing', 'exercise'])
            ->where('user_id', $user->id)
            ->orderBy('test_date', 'desc')
            ->get()
            ->groupBy('testing_id');

        $formattedHistory = $testResults->map(function ($results, $testingId) {
            $testing = $results->first()->testing;
            $latestResult = $results->first();

            // Группируем результаты по упражнениям для этого теста
            $exercisesResults = $results->map(function ($result) {
                return [
                    'exercise_id' => $result->exercise_id,
                    'exercise_description' => $result->exercise ? $result->exercise->description : null,
                    'result_value' => $result->result_value,
                    'pulse' => $result->pulse,
                    'test_date' => $result->test_date->format('Y-m-d H:i:s'),
                ];
            })->values();

            return [
                'testing_id' => $testingId,
                'testing_title' => $testing ? $testing->title : 'Тест удален',
                'last_completed_at' => $latestResult->test_date->format('Y-m-d H:i:s'),
                'total_attempts' => $results->count(),
                'exercises_results' => $exercisesResults,
            ];
        })->values();

        // Также можно добавить статистику по тестам
        $statistics = [
            'total_tests_completed' => $testResults->count(),
            'unique_tests_completed' => $testResults->keys()->count(),
            'last_test_date' => $testResults->isNotEmpty()
                ? $testResults->flatten()->first()->test_date->format('Y-m-d H:i:s')
                : null,
        ];

        $data = [
            'statistics' => $statistics,
            'history' => $formattedHistory,
        ];

        return ApiResponse::data($data);
    }
}
