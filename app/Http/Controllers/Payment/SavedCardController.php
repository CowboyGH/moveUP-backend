<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\SaveCardRequest;
use App\Models\SavedCard;
use App\Services\Payment\CardService;
use Illuminate\Http\JsonResponse;

class SavedCardController extends Controller
{
    public function __construct(
        protected CardService $cardService
    ) {}

    /**
     * Сохранение новой карты
     */
    public function simpleSaveCard(SaveCardRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Пользователь не авторизован', 401);
        }
        $data = $request->validated();

        try {
            $card = $this->cardService->saveCard($user, $data);

            return $this->successResponse(
                'Карта успешно сохранена',
                [
                    'card_saved' => true,
                    'card' => [
                        'id' => $card->id,
                        'last_four' => $card->card_last_four,
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при сохранении карты: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Получение списка сохраненных карт
     */
    public function getSavedCards(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Пользователь не авторизован', 401);
        }
        $cards = $this->cardService->getUserCards($user);
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

        if (!$user) {
            return $this->errorResponse('Пользователь не авторизован', 401);
        }

        if ($this->cardService->deleteCard($user, $cardId)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Карта успешно удалена'
            ]);
        }
        return $this->errorResponse('Карта не найдена', 404);
    }

    /**
     * Установка карты по умолчанию
     */
    public function setDefaultCard(int $cardId): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Пользователь не авторизован', 401);
        }
        try {
            SavedCard::where('user_id', $user->id)->update(['is_default' => false]);
            $card = $this->cardService->getSavedCard($user, $cardId);

            if (!$card) {
                return $this->errorResponse('Карта не найдена', 404);
            }
            $card->update(['is_default' => true]);
            return response()->json([
                'status' => 'success',
                'message' => 'Основная карта изменена'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при изменении основной карты',
                500
            );
        }
    }
    /**
     * Ответы для функций
     */
    private function successResponse(string $message, array $extraData = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message
        ], $extraData));
    }
    private function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}
