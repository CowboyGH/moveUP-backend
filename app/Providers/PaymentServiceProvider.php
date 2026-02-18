<?php

namespace App\Providers;

use App\Models\SavedCard;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('payment', function ($app) {
            return $this;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
    public function saveCard(User $user, array $cardData, bool $isDefault = false): ?SavedCard
    {
        try {
            if (!isset($cardData['card_number']) || empty($cardData['card_number'])) {
                Log::error('Cannot save card: card number is missing', [
                    'user_id' => $user->id
                ]);
                return null;
            }

            $cardHash = $this->hashCardNumber($cardData['card_number']);

            $existingCard = SavedCard::where('user_id', $user->id)
                ->where('card_number_hash', $cardHash)
                ->first();

            if ($existingCard) {
                Log::info('Card already exists for user', [
                    'user_id' => $user->id,
                    'card_id' => $existingCard->id
                ]);

                if ($isDefault && !$existingCard->is_default) {
                    SavedCard::where('user_id', $user->id)
                        ->update(['is_default' => false]);

                    $existingCard->update(['is_default' => true]);
                }

                return $existingCard;
            }

            if ($isDefault) {
                SavedCard::where('user_id', $user->id)
                    ->update(['is_default' => false]);
            }

            $savedCard = SavedCard::create([
                'user_id' => $user->id,
                'card_holder' => $cardData['card_holder'],
                'card_number_hash' => $cardHash,
                'card_last_four' => substr($cardData['card_number'], -4),
                'expiry_month' => $cardData['expiry_month'],
                'expiry_year' => $cardData['expiry_year'],
                'is_default' => $isDefault,
            ]);

            Log::info('Card saved successfully', [
                'user_id' => $user->id,
                'card_id' => $savedCard->id,
                'card_last_four' => $savedCard->card_last_four
            ]);

            return $savedCard;

        } catch (\Exception $e) {
            Log::error('Failed to save card', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Имитация обработки платежа
     */
    public function processPayment(User $user, Subscription $subscription, array $paymentData): array
    {
        // Имитация проверки карты
        if (!$this->validateCardData($paymentData)) {
            return [
                'success' => false,
                'message' => 'Неверные данные карты',
                'status' => 'failed'
            ];
        }

        // Имитация случайной ошибки (5% для тестирования)
        if (random_int(1, 100) <= 5) {
            return [
                'success' => false,
                'message' => 'Ошибка обработки платежа. Попробуйте позже',
                'status' => 'error'
            ];
        }

        // Генерируем тестовый transaction_id
        $transactionId = 'pay_' . uniqid() . '_' . time();

        Log::info('Payment processed successfully', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $subscription->price,
            'transaction_id' => $transactionId,
        ]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Платеж успешно обработан',
            'status' => 'completed',
            'amount' => $subscription->price,
            'processed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Получение сохраненной карты пользователя
     */
    public function getSavedCard(User $user, int $cardId): ?SavedCard
    {
        return SavedCard::where('user_id', $user->id)
            ->where('id', $cardId)
            ->first();
    }

    /**
     * Получение основной карты пользователя
     */
    public function getDefaultCard(User $user): ?SavedCard
    {
        return SavedCard::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Получение всех карт пользователя
     */
    public function getUserCards(User $user)
    {
        return SavedCard::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($card) {
                return [
                    'id' => $card->id,
                    'card_holder' => $card->card_holder,
                    'card_last_four' => $card->card_last_four,
                    'expiry_month' => $card->expiry_month,
                    'expiry_year' => $card->expiry_year,
                    'expiry_formatted' => $card->expiry_month . '/' . $card->expiry_year,
                    'is_default' => $card->is_default,
                ];
            });
    }

    /**
     * Удаление сохраненной карты
     */
    public function deleteCard(User $user, int $cardId): bool
    {
        $card = SavedCard::where('user_id', $user->id)
            ->where('id', $cardId)
            ->first();

        if (!$card) {
            return false;
        }

        return $card->delete();
    }

    /**
     * Хеширование номера карты
     */
    public function hashCardNumber(string $cardNumber): string
    {
        return bcrypt($cardNumber);
    }

    /**
     * Проверка номера карты
     */
    public function verifyCardNumber(string $cardNumber, string $hash): bool
    {
        return password_verify($cardNumber, $hash);
    }

    /**
     * Валидация данных карты
     */
    private function validateCardData(array $data): bool
    {
        // Базовая валидация формата
        if (isset($data['card_number'])) {
            // Проверка по алгоритму Луна (Luhn algorithm)
            if (!$this->validateLuhn($data['card_number'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверка по алгоритму Луна
     */
    private function validateLuhn(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);
        $sum = 0;
        $numDigits = strlen($number) - 1;
        $parity = $numDigits % 2;

        for ($i = 0; $i <= $numDigits; $i++) {
            $digit = (int)$number[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return ($sum % 10) == 0;
    }
}
