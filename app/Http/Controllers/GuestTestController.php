<?php
namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Testing;
use App\Services\GuestDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestTestController extends Controller
{
    private GuestDataService $guestService;

    public function __construct(GuestDataService $guestService)
    {
        $this->guestService = $guestService;
    }

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
        $data = [
            'attempt_id' => 'guest_' . $attemptId,
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

        return ApiResponse::data($data, 'Тест начат для гостя')
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
        $belongsToTest = $testing->testExercises()
            ->where('testing_exercises.id', $request->testing_exercise_id)
            ->exists();

        if (!$belongsToTest) {
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
        $nextExercise = $testing->testExercises()
            ->whereNotIn('testing_exercises.id', $attempt['completed_exercises'])
            ->orderBy('order_number')
            ->first();

        $responseData = [
            'saved' => true,
            'result' => [
                'testing_exercise_id' => $request->testing_exercise_id,
                'result_value' => $request->result_value,
            ],
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

        return ApiResponse::data($responseData, 'Результат сохранён для гостя');
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

        $totalExercises = $testing->testExercises()->count();
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

    /**
     * Получить историю тестов гостя
     */
    public function history(Request $request): JsonResponse
    {
        $guestId = $this->guestService->getGuestId($request);

        if (!$this->guestService->hasGuestTestResults($guestId)) {
            return ApiResponse::data([
                'statistics' => [
                    'total_attempts' => 0,
                    'completed_attempts' => 0,
                ],
                'history' => [],
            ], 'История тестов гостя');
        }

        $tests = $this->guestService->getGuestTestResults($guestId);

        // Фильтруем только завершенные тесты для истории
        $completedTests = array_filter($tests, fn($test) => ($test['status'] ?? '') === 'completed');

        // Сортируем по дате завершения (сначала новые)
        usort($completedTests, function($a, $b) {
            return strtotime($b['completed_at'] ?? '0') - strtotime($a['completed_at'] ?? '0');
        });

        $formattedHistory = array_map(function($test) {
            $testing = Testing::find($test['testing_id']);

            return [
                'attempt_id' => $test['attempt_id'],
                'testing_id' => $test['testing_id'],
                'testing_title' => $testing->title ?? 'Тест удален',
                'completed_at' => $test['completed_at'] ?? null,
                'pulse' => $test['pulse'] ?? null,
                'exercises_count' => count($test['results'] ?? []),
            ];
        }, $completedTests);

        $statistics = [
            'total_attempts' => count($tests),
            'completed_attempts' => count($completedTests),
            'last_test_date' => !empty($formattedHistory) ? $formattedHistory[0]['completed_at'] : null,
        ];

        return ApiResponse::data([
            'statistics' => $statistics,
            'history' => $formattedHistory,
        ], 'История тестов гостя');
    }

    /**
     * Сбросить результаты тестов гостя
     */
    public function reset(Request $request): JsonResponse
    {
        $guestId = $this->guestService->getGuestId($request);

        if ($this->guestService->hasGuestTestResults($guestId)) {
            $this->guestService->clearGuestTestResults($guestId);
        }

        return ApiResponse::success('Результаты тестов гостя сброшены', null);
    }
}
