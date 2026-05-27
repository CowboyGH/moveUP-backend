<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\SubscriptionPaymentRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Subscription;
use App\Services\Billing\CardService;
use App\Services\Billing\PaymentService;
use App\Services\Billing\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CardService $cardService,
        private readonly SubscriptionService $subscriptionService
    ) {}

    public function processPayment(SubscriptionPaymentRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }

        $validated = $request->validated();

        try {
            $subscription = Subscription::where('id', $validated['subscription_id'])
                ->where('is_active', true)
                ->first();

            if (!$subscription) {
                return ApiResponse::error(ErrorResponse::NOT_FOUND, 'Подписка не найдена', 404);
            }

            $cardData = $this->paymentService->getCardData($user, $validated);

            if (!$cardData) {
                return ApiResponse::error(ErrorResponse::PAYMENT_FAILED, 'Не удалось получить данные карты', 400);
            }

            $savedCard = null;
            $saveCard = filter_var($validated['save_card'], FILTER_VALIDATE_BOOLEAN);

            if (!$validated['use_saved_card'] && $saveCard) {
                $savedCard = $this->cardService->createOrGetCard($user, $cardData, true);
            }

            if ($validated['use_saved_card'] && isset($validated['saved_card_id'])) {
                $savedCard = $this->cardService->getSavedCard($user, $validated['saved_card_id']);
                if (!$savedCard) {
                    return ApiResponse::error(ErrorResponse::NOT_FOUND, 'Сохраненная карта не найдена', 404);
                }
            }

            $paymentResult = $this->paymentService->processPayment($user, $subscription, $cardData);

            if (!$paymentResult['success']) {
                return ApiResponse::error(ErrorResponse::PAYMENT_FAILED, $paymentResult['message'], 400);
            }

            $userSubscription = $this->subscriptionService->activateSubscription(
                $user,
                $subscription,
                $paymentResult['transaction_id'],
                $savedCard
            );

            $response = [
                'transaction_id' => $paymentResult['transaction_id'],
                'subscription' => [
                    'id' => $subscription->id,
                    'name' => $subscription->name,
                    'price' => $subscription->price,
                ],
                'user_subscription' => [
                    'id' => $userSubscription->id,
                    'start_date' => $userSubscription->start_date->format('Y-m-d'),
                    'end_date' => $userSubscription->end_date->format('Y-m-d'),
                    'is_active' => $userSubscription->is_active,
                ],
                'card_saved' => !is_null($savedCard),
            ];

            if ($savedCard) {
                $response['saved_card'] = [
                    'id' => $savedCard->id,
                    'last_four' => $savedCard->card_last_four,
                    'is_default' => $savedCard->is_default,
                ];
            }

            return ApiResponse::success('Подписка успешно оформлена', $response);

        } catch (\Exception $e) {
            Log::error('Payment processing failed', ['exception' => $e]);

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обработке платежа. Попробуйте позже.',
                500
            );
        }
    }
}
