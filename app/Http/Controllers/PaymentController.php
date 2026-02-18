<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionPaymentRequest;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Requests\SaveCardRequest;
use App\Models\SavedCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentProvider;

    public function __construct()
    {
        $this->paymentProvider = app('payment');
    }

    /**
     * ОФОРМЛЕНИЕ ПОДПИСКИ + СОХРАНЕНИЕ КАРТЫ (ЕСЛИ НУЖНО)
     */
    public function subscribe(SubscriptionPaymentRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // Получаем данные из запроса
        $data = $request->all();

        // Преобразуем булевы значения
        $saveCard = $this->parseBoolean($data['save_card'] ?? false);
        $useSavedCard = $this->parseBoolean($data['use_saved_card'] ?? false);

        try {
            DB::beginTransaction();

            // 1. Получаем подписку
            $subscription = Subscription::findOrFail($data['subscription_id']);

            // 2. Проверяем, нет ли уже активной подписки
            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('end_date', '>', now())
                ->first();

            if ($activeSubscription) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'У вас уже есть активная подписка'
                ], 400);
            }

            // 3. Получаем данные для оплаты
            $paymentData = null;
            $savedCard = null;

            if ($useSavedCard) {
                // Используем сохраненную карту
                $savedCard = SavedCard::where('user_id', $user->id)
                    ->where('id', $data['saved_card_id'])
                    ->first();

                if (!$savedCard) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Сохраненная карта не найдена'
                    ], 404);
                }

                $paymentData = [
                    'card_number' => '****' . $savedCard->card_last_four,
                    'card_holder' => $savedCard->card_holder,
                    'expiry_month' => $savedCard->expiry_month,
                    'expiry_year' => $savedCard->expiry_year,
                ];
            } else {
                // Используем новую карту
                $paymentData = [
                    'card_number' => $data['card_number'],
                    'card_holder' => $data['card_holder'],
                    'expiry_month' => $data['expiry_month'],
                    'expiry_year' => $data['expiry_year'],
                    'cvv' => $data['cvv'],
                ];
            }

            // 4. Обрабатываем платеж через провайдер
            $paymentResult = $this->paymentProvider->processPayment(
                $user,
                $subscription,
                $paymentData
            );

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'],
                    'payment_status' => $paymentResult['status']
                ], 400);
            }

            // 5. Деактивируем старые подписки
            UserSubscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // 6. Создаем новую подписку
            $userSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'start_date' => now(),
                'end_date' => now()->addDays($subscription->duration_days),
                'is_active' => true,
            ]);

            // 7. Сохраняем карту, если нужно (только для новой карты)
            $savedCardResult = null;
            if (!$useSavedCard && $saveCard) {
                // Проверяем, есть ли уже такая карта
                $cardHash = bcrypt($data['card_number']);
                $existingCard = SavedCard::where('user_id', $user->id)
                    ->where('card_last_four', substr($data['card_number'], -4))
                    ->first();

                if (!$existingCard) {
                    // Сохраняем новую карту
                    $savedCardResult = SavedCard::create([
                        'user_id' => $user->id,
                        'card_holder' => $data['card_holder'],
                        'card_number_hash' => $cardHash,
                        'card_last_four' => substr($data['card_number'], -4),
                        'expiry_month' => $data['expiry_month'],
                        'expiry_year' => $data['expiry_year'],
                        'is_default' => !SavedCard::where('user_id', $user->id)->exists(),
                    ]);
                } else {
                    $savedCardResult = $existingCard;
                }
            }

            DB::commit();

            // 8. Формируем ответ
            $response = [
                'success' => true,
                'message' => 'Подписка успешно оформлена',
                'data' => [
                    'subscription' => [
                        'id' => $userSubscription->id,
                        'name' => $subscription->name,
                        'start_date' => $userSubscription->start_date->format('Y-m-d'),
                        'end_date' => $userSubscription->end_date->format('Y-m-d'),
                    ],
                    'payment' => [
                        'transaction_id' => $paymentResult['transaction_id'],
                        'amount' => (float) $subscription->price,
                        'status' => $paymentResult['status'],
                    ],
                ],
            ];

            // Добавляем информацию о карте
            if ($useSavedCard) {
                $response['data']['card_used'] = [
                    'id' => $savedCard->id,
                    'last_four' => $savedCard->card_last_four,
                    'is_default' => $savedCard->is_default,
                ];
                $response['data']['card_saved'] = false; // Не сохраняли новую
            } else {
                $response['data']['card_used'] = [
                    'last_four' => substr($data['card_number'], -4),
                ];
                $response['data']['card_saved'] = $savedCardResult ? true : false;

                if ($savedCardResult) {
                    $response['data']['saved_card'] = [
                        'id' => $savedCardResult->id,
                        'last_four' => $savedCardResult->card_last_four,
                        'is_default' => $savedCardResult->is_default,
                    ];
                }
            }

            // Добавляем список всех карт пользователя
            $response['data']['all_cards'] = $this->paymentProvider->getUserCards($user);

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Subscription payment failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при оформлении подписки: ' . $e->getMessage()
            ], 500);
        }
    }


    public function simpleSaveCard(SaveCardRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // Получаем все данные из запроса
        $data = $request->all();

        // Проверяем save_card (поддерживаем разные форматы)
        $saveCard = false;
        if (isset($data['save_card'])) {
            if ($data['save_card'] === 'true' || $data['save_card'] === '1' || $data['save_card'] === true) {
                $saveCard = true;
            }
        }

        // Если чекбокс не отмечен - не сохраняем карту
        if (!$saveCard) {
            return response()->json([
                'success' => true,
                'message' => 'Карта не сохранена',
                'card_saved' => false
            ]);
        }

        // Сохраняем карту (очень просто)
        try {
            $card = SavedCard::create([
                'user_id' => $user->id,
                'card_holder' => $data['card_holder'],
                'card_number_hash' => bcrypt($data['card_number']),
                'card_last_four' => substr($data['card_number'], -4),
                'expiry_month' => $data['expiry_month'],
                'expiry_year' => $data['expiry_year'],
                'is_default' => !SavedCard::where('user_id', $user->id)->exists(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Карта успешно сохранена',
                'card_saved' => true,
                'card' => [
                    'id' => $card->id,
                    'last_four' => $card->card_last_four,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении карты: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение списка сохраненных карт пользователя
     */
    public function getSavedCards(): JsonResponse
    {
        $user = auth()->user();
        $cards = $this->paymentProvider->getUserCards($user);

        return response()->json([
            'status' => 'success',
            'data' => $cards
        ]);
    }

    /**
     * Удаление сохраненной карты
     */
    public function deleteCard(int $cardId): JsonResponse
    {
        $user = auth()->user();

        if ($this->paymentProvider->deleteCard($user, $cardId)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Карта успешно удалена'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Карта не найдена'
        ], 404);
    }

    /**
     * Установка карты по умолчанию
     */
    public function setDefaultCard(int $cardId): JsonResponse
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            // Сбрасываем флаг у всех карт
            SavedCard::where('user_id', $user->id)
                ->update(['is_default' => false]);

            // Устанавливаем новую основную карту
            $card = SavedCard::where('user_id', $user->id)
                ->where('id', $cardId)
                ->first();

            if (!$card) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Карта не найдена'
                ], 404);
            }

            $card->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Основная карта изменена'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при изменении основной карты'
            ], 500);
        }
    }

    /**
     * Получение данных для платежа
     */
    private function getPaymentData($user, array $validated): ?array
    {
        // Если используем сохраненную карту
        if (isset($validated['use_saved_card']) && $validated['use_saved_card']) {
            $savedCard = $this->paymentProvider->getSavedCard($user, $validated['saved_card_id']);

            if (!$savedCard) {
                return null;
            }

            return [
                'card_number' => '****' . $savedCard->card_last_four,
                'card_holder' => $savedCard->card_holder,
                'expiry_month' => $savedCard->expiry_month,
                'expiry_year' => $savedCard->expiry_year,
            ];
        }

        // Используем новые данные карты
        return [
            'card_number' => $validated['card_number'],
            'card_holder' => $validated['card_holder'],
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
            'cvv' => $validated['cvv'] ?? null,
        ];
    }
}
