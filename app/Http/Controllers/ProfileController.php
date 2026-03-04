<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Models\SavedCard;
use App\Services\PhaseService;
use App\Services\Payment\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use Illuminate\Http\UploadedFile;

class ProfileController extends Controller
{
    protected PhaseService $phaseService;
    protected CardService $cardService;

    public function __construct(PhaseService $phaseService, CardService $cardService)
    {
        $this->phaseService = $phaseService;
        $this->cardService = $cardService;
    }

    /**
     * Получить полный профиль пользователя
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();

        // Загружаем связанные данные
        $user->load(['userParameters.goal', 'userParameters.level', 'userParameters.equipment']);

        // Получаем активные подписки
        $activeSubscription = $user->userSubscriptions()
            ->with('subscription')
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->first();

        // Получаем текущую фазу
        $phaseProgress = $this->phaseService->getUserPhaseProgress($user);

        // Получаем сохраненные карты
        $cards = $this->cardService->getUserCards($user);

        // Статистика (заглушка)
        $statistics = [
            'volume' => [
                'total' => 1250,
                'by_month' => [
                    ['month' => '2024-01', 'value' => 320],
                    ['month' => '2024-02', 'value' => 450],
                    ['month' => '2024-03', 'value' => 480],
                ]
            ],
            'frequency' => [
                'total_workouts' => 24,
                'average_per_week' => 3.2,
                'current_streak' => 5,
                'max_streak' => 12,
            ],
            'trend' => [
                'direction' => 'up',
                'percentage' => 15,
                'compared_to' => 'last_month',
            ],
            'categories' => [
                ['name' => 'Силовые', 'count' => 15, 'percentage' => 62.5],
                ['name' => 'Кардио', 'count' => 6, 'percentage' => 25],
                ['name' => 'Растяжка', 'count' => 3, 'percentage' => 12.5],
            ],
        ];

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                'email_verified' => !is_null($user->email_verified_at),
            ],
            'parameters' => $user->userParameters ? [
                'goal' => $user->userParameters->goal?->name,
                'level' => $user->userParameters->level?->name,
                'equipment' => $user->userParameters->equipment?->name,
                'height' => $user->userParameters->height,
                'weight' => $user->userParameters->weight,
                'age' => $user->userParameters->age,
                'gender' => $user->userParameters->gender,
            ] : null,
            'subscriptions' => [
                'active' => $activeSubscription ? [
                    'id' => $activeSubscription->id,
                    'name' => $activeSubscription->subscription->name,
                    'price' => $activeSubscription->subscription->price,
                    'start_date' => $activeSubscription->start_date->format('Y-m-d'),
                    'end_date' => $activeSubscription->end_date->format('Y-m-d'),
                    'days_left' => max(0, now()->diffInDays($activeSubscription->end_date, false)),
                ] : null,
                'history' => [],
            ],
            'phase' => $phaseProgress,
            'cards' => $cards,
            'statistics' => $statistics,
        ];

        return ApiResponse::data($data);
    }

    /**
     * Обновить профиль (только имя и email)
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update($data);

        return ApiResponse::success('Профиль успешно обновлен', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ]);
    }

    /**
     * Сменить пароль
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        // Проверяем старый пароль
        if (!Hash::check($data['old_password'], $user->password)) {
            return ApiResponse::error(
                ErrorResponse::VALIDATION_FAILED,
                'Неверный текущий пароль',
                400
            );
        }

        // Обновляем пароль
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        return ApiResponse::success('Пароль успешно изменен');
    }

    /**
     * Удалить профиль
     */
    public function destroy(): JsonResponse
    {
        $user = auth()->user();

        // Удаляем аватар
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $user->delete();

        return ApiResponse::success('Профиль успешно удален');
    }

    /**
     * Статистика (заглушка)
     */
    public function statistics(): JsonResponse
    {
        // Заглушка, как в show методе
        $statistics = [
            'volume' => [
                'total' => 1250,
                'by_month' => [
                    ['month' => '2024-01', 'value' => 320],
                    ['month' => '2024-02', 'value' => 450],
                    ['month' => '2024-03', 'value' => 480],
                ]
            ],
            'frequency' => [
                'total_workouts' => 24,
                'average_per_week' => 3.2,
                'current_streak' => 5,
                'max_streak' => 12,
            ],
            'trend' => [
                'direction' => 'up',
                'percentage' => 15,
                'compared_to' => 'last_month',
            ],
            'categories' => [
                ['name' => 'Силовые', 'count' => 15, 'percentage' => 62.5],
                ['name' => 'Кардио', 'count' => 6, 'percentage' => 25],
                ['name' => 'Растяжка', 'count' => 3, 'percentage' => 12.5],
            ],
        ];

        return ApiResponse::data($statistics);
    }

    /**
     * Загрузить/обновить аватар
     */
    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $user = auth()->user();

        try {
            // Удаляем старый аватар если есть
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Сохраняем новый аватар
            $path = $request->file('avatar')->store('avatars', 'public');

            // Обновляем пользователя
            $user->update(['avatar' => $path]);

            return ApiResponse::success('Аватар успешно загружен', [
                'avatar_url' => asset('storage/' . $path),
                'avatar_path' => $path,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при загрузке аватара',
                500
            );
        }
    }

    /**
     * Удаление аватара
     */
    public function deleteAvatar(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->avatar) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Аватар не найден',
                404
            );
        }

        try {
            // Удаляем файл
            Storage::disk('public')->delete($user->avatar);

            // Обновляем пользователя
            $user->update(['avatar' => null]);

            return ApiResponse::success('Аватар удален', [
                'avatar_url' => null
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при удалении аватара',
                500
            );
        }
    }

    /**
     * Получение аватара по ID пользователя (публичный доступ)
     */
    public function getAvatar(int $userId)
    {
        $user = User::find($userId);

        if (!$user || !$user->avatar) {
            return $this->getDefaultAvatar();
        }

        $path = Storage::disk('public')->path($user->avatar);

        if (!file_exists($path)) {
            return $this->getDefaultAvatar();
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=86400'
        ]);
    }

    /**
     * Получение дефолтного аватара
     */
    private function getDefaultAvatar()
    {
        $defaultPath = public_path('images/default-avatar.png');
        $defaultJpgPath = public_path('images/default-avatar.jpg');

        if (file_exists($defaultPath)) {
            return response()->file($defaultPath, [
                'Content-Type' => mime_content_type($defaultPath),
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        if (file_exists($defaultJpgPath)) {
            return response()->file($defaultJpgPath, [
                'Content-Type' => mime_content_type($defaultJpgPath),
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        // Если нет дефолтного аватара, возвращаем JSON ошибку
        return response()->json([
            'code' => ErrorResponse::NOT_FOUND,
            'message' => 'Аватар не найден'
        ], 404);
    }
}
