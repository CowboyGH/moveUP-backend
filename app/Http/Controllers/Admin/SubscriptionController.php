<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Admin\Subscription\UpdateSubscriptionImageRequest;
use App\Http\Requests\Admin\Subscription\UpdateSubscriptionRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::all();

        return ApiResponse::data($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = Subscription::create($request->validated());

        if($request->hasFile('image')){
            $path = $request->file('image')->store('subscriptions', 'public');
            $subscription->update(['image' => $path]);
        }

        $data = [
            'id' => $subscription->id,
            'name' => $subscription->name,
            'description' => $subscription->description,
            'image' => $subscription->image,
            'price' => number_format($subscription->price, 2, '.', ''),
            'duration_days' => $subscription->duration_days,
            'is_active' => $subscription->is_active,
            'created_at' => $subscription->created_at?->toISOString(),
            'updated_at' => $subscription->updated_at?->toISOString(),
        ];

        return ApiResponse::success('Подписка успешно создана', $data, 201);
    }

    public function show(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Подписка не найдена',
                404
            );
        }

        return ApiResponse::data($subscription);
    }

    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Подписка не найдена',
                404
            );
        }
        $subscription->update($request->validated());

        return ApiResponse::success('Подписка успешно обновлена', $subscription);
    }
    public function updateImage(UpdateSubscriptionImageRequest $request, int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ApiResponse::error(ErrorResponse::NOT_FOUND, 'Подписка не найдена', 404);
        }
        if ($subscription->getRawOriginal('image')) {
            Storage::disk('public')->delete($subscription->getRawOriginal('image'));
        }
        $path = $request->file('image')->store('subscriptions', 'public');
        $subscription->update(['image' => $path]);

        return ApiResponse::success('Изображение подписки обновлено', $subscription);
    }

    public function destroy(int $id): JsonResponse
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Подписка не найдена',
                404
            );
        }

        if ($subscription->userSubscriptions()->where('is_active', true)->exists()) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить подписку, которая используется пользователями',
                422
            );
        }
        if ($subscription->getRawOriginal('image')) {
            Storage::disk('public')->delete($subscription->getRawOriginal('image'));
        }

        $subscription->delete();

        return ApiResponse::success('Подписка успешно удалена');
    }
}
