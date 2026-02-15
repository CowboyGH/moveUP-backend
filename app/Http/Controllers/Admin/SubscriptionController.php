<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Admin\Subscription\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::all();

        return response()->json([
            'status' => 'success',
            'data' => $subscriptions
        ]);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = Subscription::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Подписка успешно создана',
            'data' => [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'description' => $subscription->description,
                'price' => number_format($subscription->price, 2, '.', ''),
                'duration_days' => $subscription->duration_days,
                'is_active' => $subscription->is_active,
                'created_at' => $subscription->created_at?->toISOString(),
                'updated_at' => $subscription->updated_at?->toISOString(),
            ]
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Подписка не найдена'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $subscription
        ]);
    }

    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Подписка не найдена'
            ], 404);
        }

        $subscription->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Подписка успешна обновлена',
            'data' => $subscription
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Подписка не найдена'
            ], 404);
        }

        if ($subscription->userSubscriptions()->where('is_active', true)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Нельзя удалить подписку, которая используется пользователями'
            ], 422);
        }

        $subscription->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Подписка успешно удалена'
        ]);
    }
}
