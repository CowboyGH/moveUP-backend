<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Responses\ApiResponse;
use App\Models\TestAttempt;
use App\Models\UserWorkout;
use App\Services\Billing\CardService;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(
        private readonly PhaseService $phaseService,
        private readonly CardService $cardService
    ) {}

    public function show(): JsonResponse
    {
        $user = auth()->user();

        $user->load(['userParameters.goal', 'userParameters.level', 'userParameters.equipment']);

        $activeSubscription = $user->userSubscriptions()
            ->with('subscription')
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->first();

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

        $phaseProgress = $this->phaseService->getUserPhaseProgress($user);
        $cards = $this->cardService->getUserCards($user);

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
                'history' => $subscriptionsHistory,
            ],
            'workouts' => [
                'history' => $workoutsHistory,
            ],
            'tests' => [
                'history' => $testsHistory,
            ],
            'phase' => $phaseProgress,
            'cards' => $cards,
        ];

        return ApiResponse::success('success', $data);
    }

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

    public function destroy(): JsonResponse
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $user->delete();

        return ApiResponse::success('Профиль успешно удален');
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
