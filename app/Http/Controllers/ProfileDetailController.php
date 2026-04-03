<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Models\TestAttempt;
use App\Models\UserWorkout;
use App\Services\PhaseService;
use App\Services\Payment\CardService;
use Illuminate\Http\JsonResponse;

class ProfileDetailController extends Controller
{
    protected PhaseService $phaseService;
    protected CardService $cardService;

    public function __construct(PhaseService $phaseService, CardService $cardService)
    {
        $this->phaseService = $phaseService;
        $this->cardService = $cardService;
    }

    public function user(): JsonResponse
    {
        $user = auth()->user();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
            'email_verified' => !is_null($user->email_verified_at),
        ];

        return ApiResponse::success('success', $data);
    }

    public function activeSubscription(): JsonResponse
    {
        $user = auth()->user();

        $activeSubscription = $user->userSubscriptions()
            ->with('subscription')
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->first();

        if (!$activeSubscription) {
            return ApiResponse::success('success', [
                'message' => 'У пользователя нет активной подписки'
            ]);
        }

        $data = [
            'id' => $activeSubscription->id,
            'name' => $activeSubscription->subscription->name,
            'price' => $activeSubscription->subscription->price,
            'start_date' => $activeSubscription->start_date->format('Y-m-d'),
            'end_date' => $activeSubscription->end_date->format('Y-m-d'),
            'days_left' => max(0, now()->diffInDays($activeSubscription->end_date, false)),
        ];

        return ApiResponse::success('success', $data);
    }

    public function myCards(): JsonResponse
    {
        $user = auth()->user();
        $cards = $this->cardService->getUserCards($user);

        if ($cards->isEmpty()) {
            return ApiResponse::success('success', [
                'message' => 'У пользователя нет сохраненных карт'
            ]);
        }

        return ApiResponse::success('success', $cards);
    }

    public function userParameters(): JsonResponse
    {
        $user = auth()->user();
        $user->load(['userParameters.goal', 'userParameters.level', 'userParameters.equipment']);

        if (!$user->userParameters) {
            return ApiResponse::success('success', [
                'message' => 'Параметры пользователя не заполнены'
            ]);
        }

        $requiredFields = ['goal', 'level', 'equipment', 'height', 'weight', 'age', 'gender'];
        $isEmpty = false;
        foreach ($requiredFields as $field) {
            if (empty($user->userParameters->$field)) {
                $isEmpty = true;
                break;
            }
        }

        if ($isEmpty) {
            return ApiResponse::success('success', [
                'message' => 'Параметры пользователя заполнены не полностью'
            ]);
        }

        $data = [
            'goal' => $user->userParameters->goal?->name,
            'level' => $user->userParameters->level?->name,
            'equipment' => $user->userParameters->equipment?->name,
            'height' => $user->userParameters->height,
            'weight' => $user->userParameters->weight,
            'age' => $user->userParameters->age,
            'gender' => $user->userParameters->gender,
        ];

        return ApiResponse::success('success', $data);
    }

    public function history(): JsonResponse
    {
        $user = auth()->user();

        $subscriptionsHistory = $user->userSubscriptions()
            ->with('subscription')
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhere('end_date', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($userSubscription) {
                return [
                    'id' => $userSubscription->id,
                    'subscription' => [
                        'id' => $userSubscription->subscription->id,
                        'name' => $userSubscription->subscription->name,
                        'price' => $userSubscription->subscription->price,
                    ],
                    'start_date' => $userSubscription->start_date->format('Y-m-d'),
                    'end_date' => $userSubscription->end_date->format('Y-m-d'),
                    'is_active' => $userSubscription->is_active,
                    'status' => $this->getSubscriptionStatus($userSubscription),
                ];
            });

        $workoutsHistory = $user->userWorkouts()
            ->with('workout')
            ->where('status', UserWorkout::STATUS_COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($userWorkout) {
                return [
                    'id' => $userWorkout->id,
                    'workout' => [
                        'id' => $userWorkout->workout?->id,
                        'title' => $userWorkout->workout?->title ?? 'Тренировка удалена',
                    ],
                    'completed_at' => $userWorkout->completed_at?->format('Y-m-d H:i:s'),
                    'duration_minutes' => $userWorkout->completed_at && $userWorkout->started_at
                        ? (int) $userWorkout->started_at->diffInMinutes($userWorkout->completed_at)
                        : null,
                ];
            });

        $testAttempts = TestAttempt::whereHas('testResults', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('testing')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $testsHistory = $testAttempts->map(function ($attempt) {
            return [
                'attempt_id' => $attempt->id,
                'testing' => [
                    'id' => $attempt->testing->id,
                    'title' => $attempt->testing->title,
                ],
                'completed_at' => $attempt->completed_at->format('Y-m-d H:i:s'),
                'pulse' => $attempt->pulse,
                'exercises_count' => $attempt->testResults->count(),
            ];
        });

        $hasData = $subscriptionsHistory->isNotEmpty() ||
            $workoutsHistory->isNotEmpty() ||
            $testsHistory->isNotEmpty();

        if (!$hasData) {
            return ApiResponse::success('success', [
                'message' => 'У пользователя пока нет истории',
                'subscriptions' => [],
                'workouts' => [],
                'tests' => [],
            ]);
        }

        $data = [
            'subscriptions' => $subscriptionsHistory,
            'workouts' => $workoutsHistory,
            'tests' => $testsHistory,
        ];

        return ApiResponse::success('success', $data);
    }

    public function phase(): JsonResponse
    {
        $user = auth()->user();
        $phaseProgress = $this->phaseService->getUserPhaseProgress($user);

        if (!$phaseProgress['has_progress']) {
            return ApiResponse::success('success', [
                'message' => 'У пользователя нет активной фазы'
            ]);
        }

        return ApiResponse::success('success', $phaseProgress);
    }

    private function getSubscriptionStatus($subscription): string
    {
        if ($subscription->is_active && $subscription->end_date->isFuture()) {
            return 'active';
        } elseif ($subscription->end_date->isPast()) {
            return 'expired';
        } elseif (!$subscription->is_active && $subscription->end_date->isFuture()) {
            return 'cancelled';
        }
        return 'inactive';
    }
}
