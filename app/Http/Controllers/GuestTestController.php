<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Testing;
use App\Services\GuestDataService;
use App\Services\Tests\TestAttemptFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestTestController extends Controller
{
    public function __construct(
        private readonly GuestDataService $guestService,
        private readonly TestAttemptFlowService $attemptFlow
    ) {}

    /**
     * Найти индекс попытки в массиве тестов
     */
    private function findAttemptIndex(array $tests, string $attemptId, ?string $status = null): ?int
    {
        foreach ($tests as $index => $test) {
            $testAttemptId = $test['attempt_id'] ?? '';
            $cleanAttemptId = str_replace('guest_', '', $attemptId);
            $cleanTestAttemptId = str_replace('guest_', '', $testAttemptId);

            if ($cleanTestAttemptId === $cleanAttemptId || $testAttemptId === $attemptId) {
                if ($status === null || ($test['status'] ?? '') === $status) {
                    return $index;
                }
            }
        }
        return null;
    }

    /**
     * Получить данные попытки по ID
     */
    private function findAttemptById(string $guestId, string $attemptId): ?array
    {
        $guestTests = $this->guestService->getGuestTestResults($guestId);
        $index = $this->findAttemptIndex($guestTests, $attemptId);

        if ($index !== null) {
            return [
                'data' => $guestTests[$index],
                'index' => $index,
                'all_tests' => $guestTests
            ];
        }
        return null;
    }

    /**
     * Начать прохождение теста для гостя
     */
    public function start(Testing $testing, Request $request): JsonResponse
    {
        if (!$testing->is_active) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Этот тест недоступен',
                403
            );
        }
        $guestId = $this->guestService->getGuestId($request);
        $firstExercise = $this->attemptFlow->firstExercise($testing);

        if (!$firstExercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'В этом тесте нет упражнений',
                404
            );
        }
        $attemptId = (string) Str::uuid();
        $attemptData = [
            'attempt_id' => $attemptId,
            'testing_id' => $testing->id,
            'started_at' => now()->toDateTimeString(),
            'status' => 'started',
            'completed_exercises' => [],
            'results' => [],
        ];
        $this->guestService->saveGuestTestResult($guestId, $attemptData);
        return ApiResponse::data(
            $this->attemptFlow->startPayload($testing, 'guest_' . $attemptId, $firstExercise),
            'Тест начат для гостя'
        )
            ->withCookie(cookie('guest_id', $guestId, 60 * 24 * 30));
    }

    /**
     * Сохранить результат упражнения для гостя
     */
    public function storeResult(Request $request, string $attemptId): JsonResponse
    {
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

        $guestId = $this->guestService->getGuestId($request);
        $attemptInfo = $this->findAttemptById($guestId, $attemptId);

        if (!$attemptInfo || $attemptInfo['data']['status'] !== 'started') {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная попытка теста не найдена',
                404
            );
        }

        $attempt = &$attemptInfo['all_tests'][$attemptInfo['index']];
        $testing = Testing::find($attempt['testing_id']);

        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }
        if (!$this->attemptFlow->exerciseBelongsToTesting($testing, $request->testing_exercise_id)) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Упражнение не принадлежит этому тесту',
                409
            );
        }

        // Проверяем, не сохранен ли уже результат
        if (in_array($request->testing_exercise_id, $attempt['completed_exercises'])) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Результат для этого упражнения уже сохранён',
                409
            );
        }
        $attempt['results'][] = [
            'testing_exercise_id' => $request->testing_exercise_id,
            'result_value' => $request->result_value,
            'saved_at' => now()->toDateTimeString(),
        ];
        $attempt['completed_exercises'][] = $request->testing_exercise_id;

        $this->guestService->updateGuestTestResults($guestId, $attemptInfo['all_tests']);
        $nextExercise = $this->attemptFlow->nextExercise($testing, $attempt['completed_exercises']);

        return ApiResponse::data(
            $this->attemptFlow->resultPayload([
                'testing_exercise_id' => $request->testing_exercise_id,
                'result_value' => $request->result_value,
            ], $nextExercise),
            'Результат сохранён для гостя'
        );
    }

    /**
     * Завершить тест для гостя
     */
    public function complete(Request $request, string $attemptId): JsonResponse
    {
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

        $guestId = $this->guestService->getGuestId($request);

        // Находим попытку
        $attemptInfo = $this->findAttemptById($guestId, $attemptId);

        if (!$attemptInfo || $attemptInfo['data']['status'] !== 'started') {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная попытка теста не найдена',
                404
            );
        }

        $attempt = &$attemptInfo['all_tests'][$attemptInfo['index']];
        $testing = Testing::find($attempt['testing_id']);

        if (!$testing) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тест не найден',
                404
            );
        }

        $totalExercises = $this->attemptFlow->totalExercises($testing);
        $completedExercises = count($attempt['completed_exercises']);

        if ($completedExercises < $totalExercises) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Не все упражнения выполнены. Осталось: ' . ($totalExercises - $completedExercises),
                409
            );
        }
        $attempt['status'] = 'completed';
        $attempt['completed_at'] = now()->toDateTimeString();
        $attempt['pulse'] = $request->pulse;

        $this->guestService->updateGuestTestResults($guestId, $attemptInfo['all_tests']);

        return ApiResponse::success('Тест успешно завершён для гостя', [
            'attempt_id' => $attemptId,
            'completed_at' => $attempt['completed_at'],
            'pulse' => $attempt['pulse'],
        ]);
    }

}
