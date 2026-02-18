<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\SavedCard;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Активация подписки для пользователя
     */
    public function activateSubscription(User $user, Subscription $subscription, string $transactionId, ?SavedCard $savedCard = null): UserSubscription
    {
        return DB::transaction(function () use ($user, $subscription, $transactionId, $savedCard) {

            $this->deactivateCurrentSubscriptions($user);

            $userSubscription = $this->createUserSubscription($user, $subscription);

            $this->createPaymentRecord($user, $subscription, $transactionId, $savedCard);

            Log::info('Subscription activated', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'transaction_id' => $transactionId,
                'end_date' => $userSubscription->end_date,
            ]);
            return $userSubscription;
        });
    }

    /**
     * Деактивация текущих активных подписок
     */
    private function deactivateCurrentSubscriptions(User $user): void
    {
        UserSubscription::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);
    }

    /**
     * Создание новой подписки пользователя
     */
    private function createUserSubscription(User $user, Subscription $subscription): UserSubscription
    {
        $startDate = now();
        $endDate = now()->addDays($subscription->duration_days);

        return UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
        ]);
    }

    /**
     * Создание записи о платеже
     */
    private function createPaymentRecord(User $user, Subscription $subscription, string $transactionId, ?SavedCard $savedCard = null): Payment
    {
        return Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'saved_card_id' => $savedCard?->id,
            'transaction_id' => $transactionId,
            'amount' => $subscription->price,
            'status' => 'completed',
            'payment_data' => [
                'card_last_four' => $savedCard?->card_last_four ?? '0000',
                'subscription_name' => $subscription->name,
                'duration_days' => $subscription->duration_days,
            ],
        ]);
    }

    /**
     * Проверка активной подписки у пользователя
     */
    public function hasActiveSubscription(User $user): bool
    {
        return UserSubscription::where('user_id', $user->id)->where('is_active', true)->where('end_date', '>', now())->exists();
    }

    /**
     * Получение активной подписки пользователя
     */
    public function getActiveSubscription(User $user): ?UserSubscription
    {
        return UserSubscription::where('user_id', $user->id)->where('is_active', true)->where('end_date', '>', now())->first();
    }
}
