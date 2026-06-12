<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Subscription;
use App\Models\UserSubscription;
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
                    'is_active' => $subscription->is_active,
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
