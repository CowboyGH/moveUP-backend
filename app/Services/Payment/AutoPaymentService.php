<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SavedCard;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoPaymentService
{
    protected $cardService;
    protected $subscriptionService;
    protected $paymentService;

    public function __construct(
        CardService $cardService,
        SubscriptionService $subscriptionService,
        PaymentService $paymentService
    ) {
        $this->cardService = $cardService;
        $this->subscriptionService = $subscriptionService;
        $this->paymentService = $paymentService;
    }

    /**
     * Проверка и обработка подписок, которые истекают сегодня
     */
    public function processExpiringSubscriptions(): void
    {
        // Находим активные подписки, которые истекают сегодня (в течение следующих 24 часов)
        $expiringSubscriptions = UserSubscription::with(['user', 'subscription'])
            ->where('is_active', true)
            ->whereBetween('end_date', [
                now()->startOfDay(),
                now()->addDay()->endOfDay()
            ])
            ->get();

        Log::info('AutoPayment: Found expiring subscriptions', [
            'count' => $expiringSubscriptions->count()
        ]);

        foreach ($expiringSubscriptions as $userSubscription) {
            $this->processAutoRenewal($userSubscription);
        }
    }

    /**
     * Обработка автоматического продления подписки
     */
    protected function processAutoRenewal(UserSubscription $userSubscription): void
    {
        $user = $userSubscription->user;
        $subscription = $userSubscription->subscription;

        Log::info('AutoPayment: Processing renewal', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'user_subscription_id' => $userSubscription->id
        ]);

        // Получаем все карты пользователя, отсортированные по умолчанию (основная первая)
        $cards = SavedCard::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($cards->isEmpty()) {
            $this->cancelSubscription($userSubscription, 'Нет сохраненных карт для автооплаты');
            return;
        }

        $paymentSuccessful = false;
        $failedCards = [];

        // Пробуем оплатить каждой картой по очереди
        foreach ($cards as $card) {
            $result = $this->attemptAutoPayment($user, $subscription, $card, $userSubscription);

            if ($result['success']) {
                $paymentSuccessful = true;
                break;
            } else {
                $failedCards[] = [
                    'card_id' => $card->id,
                    'reason' => $result['message']
                ];
            }
        }

        if (!$paymentSuccessful) {
            $this->cancelSubscription($userSubscription, 'Недостаточно средств на всех картах', $failedCards);
        }
    }

    /**
     * Попытка автоматического списания с конкретной карты
     */
    protected function attemptAutoPayment(User $user, $subscription, SavedCard $card, UserSubscription $oldSubscription): array
    {
        // Симуляция проверки наличия средств (15% вероятность недостатка средств)
        if (random_int(1, 100) <= 15) {
            Log::warning('AutoPayment: Insufficient funds simulation', [
                'user_id' => $user->id,
                'card_id' => $card->id,
                'amount' => $subscription->price
            ]);

            return [
                'success' => false,
                'message' => 'На карте недостаточно средств'
            ];
        }

        // Симуляция успешного списания (остальные 85%)
        try {
            return DB::transaction(function () use ($user, $subscription, $card, $oldSubscription) {
                // Создаем запись о платеже
                $transactionId = 'auto_' . uniqid() . '_' . time();

                $payment = Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'saved_card_id' => $card->id,
                    'transaction_id' => $transactionId,
                    'amount' => $subscription->price,
                    'status' => 'completed',
                    'payment_data' => [
                        'card_last_four' => $card->card_last_four,
                        'subscription_name' => $subscription->name,
                        'auto_payment' => true,
                        'previous_subscription_id' => $oldSubscription->id
                    ],
                ]);

                // Деактивируем старую подписку
                $oldSubscription->update(['is_active' => false]);

                // Создаем новую подписку
                $newSubscription = $this->subscriptionService->createUserSubscription($user, $subscription);

                Log::info('AutoPayment: Successfully renewed subscription', [
                    'user_id' => $user->id,
                    'card_id' => $card->id,
                    'transaction_id' => $transactionId,
                    'new_subscription_id' => $newSubscription->id
                ]);

                return [
                    'success' => true,
                    'message' => 'Подписка успешно продлена',
                    'payment' => $payment,
                    'new_subscription' => $newSubscription
                ];
            });
        } catch (\Exception $e) {
            Log::error('AutoPayment: Payment failed with exception', [
                'user_id' => $user->id,
                'card_id' => $card->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Ошибка при обработке платежа: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Отмена подписки при неудачной оплате
     */
    protected function cancelSubscription(UserSubscription $userSubscription, string $reason, array $failedCards = []): void
    {
        $userSubscription->update(['is_active' => false]);

        // Создаем запись о неудачном платеже
        Payment::create([
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->subscription_id,
            'amount' => $userSubscription->subscription->price,
            'status' => 'failed',
            'payment_data' => [
                'reason' => $reason,
                'failed_cards' => $failedCards,
                'auto_payment_attempt' => true,
                'attempted_at' => now()->toDateTimeString()
            ],
        ]);

        Log::warning('AutoPayment: Subscription cancelled', [
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->subscription_id,
            'reason' => $reason,
            'failed_cards_count' => count($failedCards)
        ]);
    }
}
