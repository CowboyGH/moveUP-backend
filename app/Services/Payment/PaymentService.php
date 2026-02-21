<?php

namespace App\Services\Payment;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $cardService;
    protected $subscriptionService;

    public function __construct(CardService $cardService, SubscriptionService $subscriptionService)
    {
        $this->cardService = $cardService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Имитация обработки платежа
     */
    public function processPayment(User $user, Subscription $subscription, array $paymentData): array
    {
        $isSavedCard = isset($paymentData['is_saved_card']) && $paymentData['is_saved_card'] === true;
        $isAutoPayment = isset($paymentData['is_auto_payment']) && $paymentData['is_auto_payment'] === true;

        // Для автооплаты пропускаем валидацию полей, так как используем сохраненные карты
        if (!$isAutoPayment && !$this->validateRequiredFields($paymentData, $isSavedCard)) {
            return [
                'success' => false,
                'message' => 'Отсутствуют обязательные поля карты',
                'status' => 'failed'
            ];
        }

        if ($isSavedCard && isset($paymentData['saved_card_id'])) {
            $savedCard = $this->cardService->getSavedCard($user, $paymentData['saved_card_id']);

            if (!$savedCard) {
                Log::warning('Saved card not found or does not belong to user', [
                    'user_id' => $user->id,
                    'card_id' => $paymentData['saved_card_id'],
                    'is_auto_payment' => $isAutoPayment
                ]);
                return [
                    'success' => false,
                    'message' => 'Сохраненная карта не найдена',
                    'status' => 'failed'
                ];
            }
        }

        // Для автооплаты своя логика проверки (10-15% вероятность отказа)
        if ($isAutoPayment) {
            // 15% вероятность недостатка средств для автооплаты
            if (random_int(1, 100) <= 15) {
                Log::info('Auto-payment failed: insufficient funds', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $subscription->price,
                    'card_id' => $paymentData['saved_card_id'] ?? null
                ]);

                return [
                    'success' => false,
                    'message' => 'На карте недостаточно средств',
                    'status' => 'insufficient_funds',
                    'is_auto_payment' => true
                ];
            }
        } else {
            // Для ручной оплаты оставляем 5% случайной ошибки
            if (random_int(1, 100) <= 5) {
                return [
                    'success' => false,
                    'message' => 'Ошибка обработки платежа. Попробуйте позже',
                    'status' => 'error'
                ];
            }
        }

        $transactionId = ($isAutoPayment ? 'auto_' : 'pay_') . uniqid() . '_' . time();

        Log::info('Payment processed successfully', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->price,
            'transaction_id' => $transactionId,
            'is_saved_card' => $isSavedCard,
            'is_auto_payment' => $isAutoPayment,
        ]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => $isAutoPayment ? 'Автоплатеж успешно обработан' : 'Платеж успешно обработан',
            'status' => 'completed',
            'amount' => $subscription->price,
            'processed_at' => now()->toDateTimeString(),
            'is_auto_payment' => $isAutoPayment,
        ];
    }

    /**
     * Простая проверка наличия обязательных полей
     */
    private function validateRequiredFields(array $data, bool $isSavedCard = false): bool
    {
        if ($isSavedCard) {
            return isset($data['card_holder']) &&
                isset($data['expiry_month']) &&
                isset($data['expiry_year']);
        }

        return isset($data['card_number']) &&
            isset($data['card_holder']) &&
            isset($data['expiry_month']) &&
            isset($data['expiry_year']) &&
            isset($data['cvv']);
    }

    /**
     * Получение данных карты из запроса
     */
    public function getCardData(User $user, array $validated, CardService $cardService): ?array
    {
        if (isset($validated['use_saved_card']) && filter_var($validated['use_saved_card'], FILTER_VALIDATE_BOOLEAN)) {
            $savedCard = $cardService->getSavedCard($user, $validated['saved_card_id']);

            if (!$savedCard) {
                return null;
            }
            return [
                'card_number' => $cardService->generateMaskedNumber($savedCard->card_last_four),
                'card_holder' => $savedCard->card_holder,
                'expiry_month' => $savedCard->expiry_month,
                'expiry_year' => $savedCard->expiry_year,
                'is_saved_card' => true,
                'saved_card_id' => $savedCard->id,
                'is_auto_payment' => false, // По умолчанию не автооплата
            ];
        }

        return [
            'card_number' => $validated['card_number'],
            'card_holder' => $validated['card_holder'],
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
            'cvv' => $validated['cvv'] ?? null,
            'is_saved_card' => false,
            'is_auto_payment' => false,
        ];
    }

    /**
     * Специальный метод для автооплаты
     */
    public function processAutoPayment(User $user, Subscription $subscription, int $savedCardId): array
    {
        $paymentData = [
            'is_saved_card' => true,
            'saved_card_id' => $savedCardId,
            'is_auto_payment' => true,
            'card_holder' => 'AUTO PAYMENT',
            'expiry_month' => '01',
            'expiry_year' => '2030',
        ];

        return $this->processPayment($user, $subscription, $paymentData);
    }
}
