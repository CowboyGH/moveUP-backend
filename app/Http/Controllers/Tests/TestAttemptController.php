<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\TestAttempt;
use App\Models\Testing;
use App\Models\TestResult;
use App\Models\User;
use App\Services\Tests\TestAttemptFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TestAttemptController extends Controller
{
    public function __construct(
        private readonly WorkoutGeneratorService $workoutGenerator,
        private readonly TestAttemptFlowService $attemptFlow
    ) {}

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

        $firstExercise = $this->attemptFlow->firstExercise($testing);

        if (!$firstExercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'В этом тесте нет упражнений',
                404
            );
        }

        return ApiResponse::data(
            $this->attemptFlow->startPayload($testing, $attempt->id, $firstExercise),
            'Тест начат'
        );
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

        if (!$this->attemptFlow->exerciseBelongsToTesting($attempt->testing, $request->testing_exercise_id)) {
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

        $nextExercise = $this->attemptFlow->nextExercise($attempt->testing, $completedIds);

        return ApiResponse::data(
            $this->attemptFlow->resultPayload($result, $nextExercise),
            'Результат сохранён'
        );
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

        $completedExercises = TestResult::where('test_attempt_id', $attempt->id)->count();
        $remainingExercises = $this->attemptFlow->remainingExercises($attempt->testing, $completedExercises);

        if (!$this->attemptFlow->canComplete($attempt->testing, $completedExercises)) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Не все упражнения выполнены. Осталось: ' . $remainingExercises,
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

        $user = auth()->user();
        $this->regenerateWorkoutsAfterTest($user);

        return ApiResponse::success(
            'Тест успешно завершён! Тренировки сгенерированы!',
            $this->attemptFlow->completePayload($attempt->id, $attempt->completed_at, $attempt->pulse)
        );
    }

    private function regenerateWorkoutsAfterTest(User $user): void
    {
        $currentProgress = $user->currentProgress();

        if (!$currentProgress) {
            Log::info("Пользователь {$user->id} завершил тест, но у него нет активной фазы");
            return;
        }

        // Удаляем старые незавершенные тренировки
        $deleted = $user->userWorkouts()
            ->where('status', 'started')
            ->delete();

        Log::info("Удалено {$deleted} старых тренировок пользователя {$user->id} после завершения теста");

        // Генерируем новые тренировки с учетом результатов теста
        $workouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

        if ($workouts->isNotEmpty()) {
            $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);
            Log::info("Сгенерировано {$workouts->count()} тренировок для пользователя {$user->id} после завершения теста");
        }
    }
}
