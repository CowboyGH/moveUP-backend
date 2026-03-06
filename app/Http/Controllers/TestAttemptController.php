<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestAttempt;
use App\Models\Testing;
use App\Models\TestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestAttemptController extends Controller
{
    /**
     * Начать прохождение теста
     */
    public function start(Testing $testing): JsonResponse
    {
        if (!$testing->is_active) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Этот тест недоступен',
                403
            );
        }

        $attempt = TestAttempt::create([
            'testing_id' => $testing->id,
            'started_at' => now(),
        ]);

        $firstExercise = $testing->testExercises()
            ->orderBy('order_number')
            ->first();

        if (!$firstExercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'В этом тесте нет упражнений',
                404
            );
        }

        $data = [
            'attempt_id' => $attempt->id,
            'testing' => [
                'id' => $testing->id,
                'title' => $testing->title,
                'description' => $testing->description,
                'duration_minutes' => $testing->duration_minutes,
                'image' => $testing->image,
                'total_exercises' => $testing->testExercises()->count(),
            ],
            'current_exercise' => [
                'id' => $firstExercise->id,
                'description' => $firstExercise->description,
                'image' => $firstExercise->image,
                'order_number' => $firstExercise->pivot->order_number,
            ],
        ];

        return ApiResponse::data($data, 'Тест начат');
    }

    /**
     * Сохранить результат выполнения упражнения
     */
    public function storeResult(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ($attempt->completed_at) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Тест уже завершён',
                409
            );
        }

        $validator = Validator::make($request->all(), [
            'testing_exercise_id' => 'required|exists:testing_exercises,id',
            'result_value' => 'required|integer|between:1,4',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Ошибка валидации',
                422,
                $validator->errors()->toArray()
            );
        }

        $belongsToTest = DB::table('testing_test_exercises')
            ->where('testing_id', $attempt->testing_id)
            ->where('testing_exercise_id', $request->testing_exercise_id)
            ->exists();

        if (!$belongsToTest) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Упражнение не принадлежит этому тесту',
                409
            );
        }

        $alreadySaved = TestResult::where('test_attempt_id', $attempt->id)
            ->where('testing_exercise_id', $request->testing_exercise_id)
            ->exists();

        if ($alreadySaved) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Результат для этого упражнения уже сохранён',
                409
            );
        }

        $result = TestResult::create([
            'user_id' => auth()->id(),
            'testing_id' => $attempt->testing_id,
            'test_attempt_id' => $attempt->id,
            'testing_exercise_id' => $request->testing_exercise_id,
            'result_value' => $request->result_value,
            'test_date' => now()->toDateString(),
        ]);

        $completedIds = TestResult::where('test_attempt_id', $attempt->id)
            ->pluck('testing_exercise_id')
            ->toArray();

        $nextExercise = $attempt->testing->testExercises()
            ->whereNotIn('testing_exercises.id', $completedIds)
            ->orderBy('order_number')
            ->first();

        $responseData = [
            'saved' => true,
            'result' => $result,
        ];

        if ($nextExercise) {
            $responseData['next_exercise'] = [
                'id' => $nextExercise->id,
                'description' => $nextExercise->description,
                'image' => $nextExercise->image,
                'order_number' => $nextExercise->pivot->order_number,
            ];
        } else {
            $responseData['all_exercises_completed'] = true;
            $responseData['message'] = 'Все упражнения выполнены. Введите пульс для завершения теста.';
        }

        return ApiResponse::data($responseData, 'Результат сохранён');
    }

    /**
     * Завершить тест и сохранить пульс
     */
    public function complete(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ($attempt->completed_at) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Тест уже завершён',
                409
            );
        }

        $validator = Validator::make($request->all(), [
            'pulse' => 'required|integer|min:30|max:220',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Ошибка валидации',
                422,
                $validator->errors()->toArray()
            );
        }

        $totalExercises = $attempt->testing->testExercises()->count();
        $completedExercises = TestResult::where('test_attempt_id', $attempt->id)->count();

        if ($completedExercises < $totalExercises) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Не все упражнения выполнены. Осталось: ' . ($totalExercises - $completedExercises),
                409
            );
        }

        $attempt->update([
            'pulse' => $request->pulse,
            'completed_at' => now(),
        ]);

        // Опционально: обновить test_date для всех результатов
        TestResult::where('test_attempt_id', $attempt->id)
            ->update(['test_date' => now()->toDateString()]);

        return ApiResponse::success('Тест успешно завершён', [
            'attempt_id' => $attempt->id,
            'completed_at' => $attempt->completed_at,
            'pulse' => $attempt->pulse,
        ]);
    }
}
