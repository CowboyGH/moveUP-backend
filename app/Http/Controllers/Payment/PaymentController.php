<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\SubscriptionPaymentRequest;
use App\Models\Subscription;
use App\Services\Payment\CardService;
use App\Services\Payment\PaymentService;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $cardService;
    protected $subscriptionService;

    public function __construct(
        PaymentService $paymentService,
        CardService $cardService,
        SubscriptionService $subscriptionService
    ) {
        $this->paymentService = $paymentService;
        $this->cardService = $cardService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Обработка платежа за подписку
     */
    public function processPayment(SubscriptionPaymentRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $subscription = Subscription::findOrFail($validated['subscription_id']);
            $savedCard = null;
            $cardData = $this->paymentService->getCardData($user, $validated, $this->cardService);

            if (!$cardData) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось получить данные карты'
                ], 400);
            }

            $saveCard = filter_var($validated['save_card'], FILTER_VALIDATE_BOOLEAN);

            // Проверка на использование карты(новой/существующей)
            if (!$validated['use_saved_card'] && $saveCard) {
                $savedCard = $this->cardService->createOrGetCard($user, $cardData, true);
            }

            // Если используем сохраненную карту
            if ($validated['use_saved_card'] && isset($validated['saved_card_id'])) {
                $savedCard = $this->cardService->getSavedCard($user, $validated['saved_card_id']);
                if (!$savedCard) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Сохраненная карта не найдена'
                    ], 404);
                }
            }
            $paymentResult = $this->paymentService->processPayment($user, $subscription, $cardData);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'],
                    'status' => $paymentResult['status']
                ], 400);
            }

            // Активируем подписку
            $userSubscription = $this->subscriptionService->activateSubscription(
                $user,
                $subscription,
                $paymentResult['transaction_id'],
                $savedCard
            );

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Подписка успешно оформлена',
                'data' => [
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
                ]
            ];

            if ($savedCard) {
                $response['data']['saved_card'] = [
                    'id' => $savedCard->id,
                    'last_four' => $savedCard->card_last_four,
                    'is_default' => $savedCard->is_default,
                ];
            }
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обработке платежа: ' . $e->getMessage()
            ], 500);
        }
    }
}
