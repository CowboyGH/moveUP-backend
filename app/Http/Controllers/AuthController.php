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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private GuestDataService $guestService;

    public function __construct(GuestDataService $guestService)
    {
        $this->guestService = $guestService;
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

        // Переносим параметры пользователя (goal, level, equipment и т.д.)
        if ($this->guestService->hasGuestData($guestId)) {
            $this->transferGuestParameters($user, $guestId);
        }

        // Переносим результаты тестов
        if ($this->guestService->hasGuestTestResults($guestId)) {
            $this->transferGuestTestResults($user, $guestId);
        }

        // Очищаем все данные гостя
        $this->guestService->clearGuestData($guestId);

        Log::info("Перенесены все данные гостя {$guestId} пользователю {$user->id}");
    }

    /**
     * Перенести параметры пользователя (из UserParameterController)
     */
    private function transferGuestParameters(User $user, string $guestId): void
    {
        $guestData = $this->guestService->getGuestData($guestId);

        if (empty($guestData)) {
            return;
        }

        $parameters = UserParameter::firstOrNew(['user_id' => $user->id]);
        $fillableFields = ['goal_id', 'level_id', 'equipment_id', 'height', 'weight', 'age', 'gender'];
        $updated = false;

        foreach ($fillableFields as $field) {
            if (isset($guestData[$field]) && empty($parameters->$field)) {
                $parameters->$field = $guestData[$field];
                $updated = true;
            }
        }

        if ($updated) {
            $parameters->save();
            Log::info("Перенесены параметры пользователя для {$user->id}", $guestData);
        }
    }

    /**
     * Перенести результаты тестов гостя
     */
    private function transferGuestTestResults(User $user, string $guestId): void
    {
        $guestTests = $this->guestService->getGuestTestResults($guestId);
        $completedTests = array_filter($guestTests, fn($test) => ($test['status'] ?? '') === 'completed');

        if (empty($completedTests)) {
            return;
        }

        DB::transaction(function () use ($user, $completedTests) {
            foreach ($completedTests as $testData) {
                // Создаем попытку теста
                $attempt = TestAttempt::create([
                    'testing_id' => $testData['testing_id'],
                    'started_at' => $testData['started_at'] ?? now(),
                    'completed_at' => $testData['completed_at'] ?? now(),
                    'pulse' => $testData['pulse'] ?? null,
                ]);

                // Сохраняем результаты
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

                Log::info("Перенесены результаты теста для пользователя {$user->id}, попытка {$attempt->id}");
            }
        });
    }
}
