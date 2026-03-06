<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestAttempt;
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

        $attempts = TestAttempt::whereHas('testResults', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['testing', 'testResults.testingExercise.exercise'])
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $formattedHistory = $attempts->map(function ($attempt) {
            $testing = $attempt->testing;

            $exercisesResults = $attempt->testResults->map(function ($result) {
                return [
                    'testing_exercise_id' => $result->testing_exercise_id,
                    'exercise_id' => $result->testingExercise->exercise_id,
                    'exercise_description' => $result->testingExercise->exercise->description ?? null,
                    'result_value' => $result->result_value,
                    'test_date' => $result->test_date->format('Y-m-d'),
                ];
            })->values();

            return [
                'attempt_id' => $attempt->id,
                'testing_id' => $testing->id,
                'testing_title' => $testing->title,
                'completed_at' => $attempt->completed_at->format('Y-m-d H:i:s'),
                'pulse' => $attempt->pulse,
                'exercises_results' => $exercisesResults,
            ];
        })->values();

        $statistics = [
            'total_attempts' => $attempts->count(),
            'unique_tests_completed' => $attempts->pluck('testing_id')->unique()->count(),
            'last_test_date' => $attempts->isNotEmpty() ? $attempts->first()->completed_at->format('Y-m-d H:i:s') : null,
        ];

        return ApiResponse::data([
            'statistics' => $statistics,
            'history' => $formattedHistory,
        ]);
    }
}
