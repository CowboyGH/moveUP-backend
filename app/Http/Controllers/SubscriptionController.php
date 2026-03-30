<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserSubscription;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::where('is_active', 1)->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'name' => $subscription->name,
                    'description' => $subscription->description,
                    'image' => $subscription->image,
                    'price' => $subscription->price,
                    'duration_days' => $subscription->duration_days,
                ];
            });

        return ApiResponse::success('success', $subscriptions);
    }

    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::where('id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$subscription) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Подписка не найдена',
                404
            );
        }

        return ApiResponse::success('success', $subscription);
    }

    public function mySubscriptions(): JsonResponse
    {
        $user = auth()->user();

        $subscriptions = UserSubscription::with('subscription')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeSubscription = $subscriptions->where('is_active', 1)->first();

        $formattedHistory = $subscriptions->map(function ($userSubscription) {
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

        $data = [
            'active' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'name' => $activeSubscription->subscription->name,
                'end_date' => $activeSubscription->end_date->format('Y-m-d'),
                'days_left' => (int) now()->diffInDays($activeSubscription->end_date),
            ] : null,
            'history' => $formattedHistory,
        ];

        return ApiResponse::success('success', $data);
    }

    private function getSubscriptionStatus(UserSubscription $subscription): string
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

    public function cancel(): JsonResponse
    {
        $user = auth()->user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('is_active', 1)
            ->where('end_date', '>', now())
            ->with('subscription')
            ->first();

        if (!$activeSubscription) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная подписка не найдена',
                404
            );
        }

        $activeSubscription->update([
            'is_active' => 0,
            'end_date' => now(),
        ]);

        return ApiResponse::success('Подписка успешно отменена', [
            'id' => $activeSubscription->id,
            'subscription_name' => $activeSubscription->subscription->name,
            'end_date' => now()->format('d.m.Y'),
        ]);
    }
}
