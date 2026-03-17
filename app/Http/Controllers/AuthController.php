<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use App\Models\TestAttempt;
use App\Models\TestResult;
use App\Models\User;
use App\Models\Role;
use App\Models\UserParameter;
use App\Services\GuestDataService;
use App\Jobs\SendVerificationEmail;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private GuestDataService $guestService;
    private PhaseService $phaseService;
    private WorkoutGeneratorService $workoutGenerator;

    public function __construct(
        GuestDataService $guestService,
        PhaseService $phaseService,
        WorkoutGeneratorService $workoutGenerator
    ) {
        $this->guestService = $guestService;
        $this->phaseService = $phaseService;
        $this->workoutGenerator = $workoutGenerator;
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => Role::where('name', 'user')->first()->id,
        ]);
        $guestId = $this->guestService->getGuestId($request);
        $this->transferGuestDataToUser($user, $guestId);

        SendVerificationEmail::dispatch($user);

        return response()->json([
            'success' => true,
            'message' => 'Регистрация прошла успешно. Проверьте вашу почту для получения кода подтверждения.',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = Auth::attempt($credentials)) {
            return ErrorResponse::make(
                ErrorResponse::INVALID_CREDENTIALS,
                'Неверные учетные данные.',
                401
            );
        }
        $user = Auth::user();

        if (!$user->email_verified_at) {
            return ErrorResponse::make(
                ErrorResponse::EMAIL_NOT_VERIFIED,
                'Email не подтвержден.',
                403
            );
        }
        $guestId = $this->guestService->getGuestId($request);
        $this->transferGuestDataToUser($user, $guestId);

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'refresh_expires_in' => config('jwt.refresh_ttl') * 60,
            'session' => [
                'lifetime_days' => 30,
                'inactivity_limit_days' => 7,
                'access_token_expires_in_minutes' => config('jwt.ttl')
            ],
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'Успешный выход из системы.'
        ]);
    }

    public function refresh()
    {
        try {
            $newToken = Auth::refresh();

            return response()->json([
                'success' => true,
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ErrorResponse::make(
                ErrorResponse::SESSION_EXPIRED_ABSOLUTE,
                'Срок действия сессии истек. Войдите снова.',
                401
            );
        } catch (\Exception $e) {
            return ErrorResponse::make(
                ErrorResponse::UNAUTHORIZED,
                'Неавторизован.',
                401
            );
        }
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'user' => Auth::user()
        ]);
    }

    /**
     * Перенести все данные гостя пользователю
     */
    private function transferGuestDataToUser(User $user, ?string $guestId): void
    {
        if (!$guestId) {
            return;
        }

        $hasParams = false;
        $hasTests = false;

        // Переносим параметры пользователя
        if ($this->guestService->hasGuestData($guestId)) {
            $hasParams = $this->transferGuestParameters($user, $guestId);
        }

        // Переносим результаты тестов
        if ($this->guestService->hasGuestTestResults($guestId)) {
            $hasTests = $this->transferGuestTestResults($user, $guestId);
        }

        // ВАЖНО: Проверяем наличие параметров после переноса
        $user->refresh(); // Обновляем пользователя, чтобы получить свежие параметры
        $params = $user->userParameters;

        Log::info("После переноса - проверка параметров", [
            'user_id' => $user->id,
            'has_params_object' => $params ? 'да' : 'нет',
            'goal_id' => $params->goal_id ?? null,
            'level_id' => $params->level_id ?? null,
            'equipment_id' => $params->equipment_id ?? null,
            'hasParams_flag' => $hasParams
        ]);

        // Если есть параметры, создаем фазу и генерируем тренировки
        if ($params && $params->goal_id && $params->level_id && $params->equipment_id) {
            $this->ensureUserHasPhaseAndWorkouts($user);
        } else {
            Log::info("У пользователя {$user->id} нет полных параметров, фаза не создается", [
                'goal_id' => $params->goal_id ?? null,
                'level_id' => $params->level_id ?? null,
                'equipment_id' => $params->equipment_id ?? null
            ]);
        }

        // Очищаем данные гостя
        $this->guestService->clearGuestData($guestId);

        Log::info("Перенесены все данные гостя {$guestId} пользователю {$user->id}", [
            'has_params' => $hasParams,
            'has_tests' => $hasTests
        ]);
    }

    /**
     * Перенести параметры пользователя (ИСПРАВЛЕННАЯ ВЕРСИЯ)
     */
    private function transferGuestParameters(User $user, string $guestId): bool
    {
        $guestData = $this->guestService->getGuestData($guestId);

        if (empty($guestData)) {
            return false;
        }

        Log::info("Перенос параметров из гостя", [
            'guest_id' => $guestId,
            'data' => $guestData
        ]);

        $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
        $fillableFields = ['goal_id', 'level_id', 'equipment_id', 'height', 'weight', 'age', 'gender'];
        $updated = false;

        foreach ($fillableFields as $field) {
            if (isset($guestData[$field])) {
                // Убираем проверку empty() - просто переносим все данные
                $oldValue = $parameters->$field;
                $parameters->$field = $guestData[$field];

                if ($oldValue != $guestData[$field]) {
                    $updated = true;
                    Log::info("Поле {$field} обновлено", [
                        'было' => $oldValue,
                        'стало' => $guestData[$field]
                    ]);
                }
            }
        }

        if ($updated) {
            $parameters->save();
            Log::info("✅ Параметры сохранены для пользователя {$user->id}", $guestData);

            // Проверим, что сохранилось
            $parameters->refresh();
            Log::info("Проверка после сохранения", [
                'goal_id' => $parameters->goal_id,
                'level_id' => $parameters->level_id,
                'equipment_id' => $parameters->equipment_id
            ]);

            return true;
        } else {
            Log::info("Параметры не обновлялись для пользователя {$user->id}");
        }

        return false;
    }

    /**
     * Перенести результаты тестов гостя
     */
    private function transferGuestTestResults(User $user, string $guestId): bool
    {
        $guestTests = $this->guestService->getGuestTestResults($guestId);
        $completedTests = array_filter($guestTests, fn($test) => ($test['status'] ?? '') === 'completed');

        if (empty($completedTests)) {
            return false;
        }

        Log::info("Перенос тестов из гостя", [
            'guest_id' => $guestId,
            'tests_count' => count($completedTests)
        ]);

        DB::transaction(function () use ($user, $completedTests) {
            foreach ($completedTests as $testData) {
                $attempt = TestAttempt::create([
                    'testing_id' => $testData['testing_id'],
                    'started_at' => $testData['started_at'] ?? now(),
                    'completed_at' => $testData['completed_at'] ?? now(),
                    'pulse' => $testData['pulse'] ?? null,
                ]);

                if (!empty($testData['results'])) {
                    foreach ($testData['results'] as $result) {
                        TestResult::create([
                            'user_id' => $user->id,
                            'testing_id' => $testData['testing_id'],
                            'test_attempt_id' => $attempt->id,
                            'testing_exercise_id' => $result['testing_exercise_id'],
                            'result_value' => $result['result_value'],
                            'test_date' => now()->toDateString(),
                        ]);
                    }
                }

                Log::info("✅ Перенесены результаты теста для пользователя {$user->id}, попытка {$attempt->id}");
            }
        });

        return true;
    }

    /**
     * Убедиться, что у пользователя есть фаза и тренировки
     */
    private function ensureUserHasPhaseAndWorkouts(User $user): void
    {
        Log::info("🔧 Проверка наличия фазы у пользователя {$user->id}");

        // Проверяем параметры
        $params = $user->userParameters;
        if (!$params) {
            Log::error("❌ У пользователя {$user->id} нет параметров!");
            return;
        }

        if (!$params->goal_id || !$params->level_id || !$params->equipment_id) {
            Log::info("У пользователя {$user->id} не все параметры заполнены", [
                'goal_id' => $params->goal_id ?? null,
                'level_id' => $params->level_id ?? null,
                'equipment_id' => $params->equipment_id ?? null
            ]);
            return;
        }

        // Проверяем наличие фазы
        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            Log::info("Создаем начальную фазу для пользователя {$user->id}");
            $currentProgress = $this->phaseService->assignInitialPhase($user);
            Log::info("✅ Фаза создана: {$currentProgress->phase->name} (ID: {$currentProgress->phase_id})");
        } else {
            Log::info("У пользователя уже есть фаза: {$currentProgress->phase->name}");
        }

        // Проверяем наличие тренировок
        $workouts = $user->userWorkouts()->count();
        Log::info("Текущее количество тренировок: {$workouts}");

        // Если нет тренировок, генерируем
        if ($workouts == 0) {
            Log::info("🚀 Генерируем тренировки для пользователя {$user->id}");

            $generatedWorkouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

            if ($generatedWorkouts->isNotEmpty()) {
                $this->workoutGenerator->assignWorkoutsToUser($user, $generatedWorkouts);
                Log::info("✅ Сгенерировано {$generatedWorkouts->count()} тренировок");
            } else {
                Log::warning("❌ Не удалось сгенерировать тренировки");
            }
        }
    }
}
