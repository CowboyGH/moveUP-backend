<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\SaveCardRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\SavedCard;
use App\Services\Payment\CardService;
use Illuminate\Http\JsonResponse;

class SavedCardController extends Controller
{
    public function __construct(
        protected CardService $cardService
    ) {}

    public function simpleSaveCard(SaveCardRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }

        $data = $request->validated();

        try {
            $card = $this->cardService->saveCard($user, $data);

            return ApiResponse::success(
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
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при сохранении карты: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getSavedCards(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }

        $cards = $this->cardService->getUserCards($user);

        return ApiResponse::data($cards);
    }

    public function deleteCard(int $cardId): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }

        if ($this->cardService->deleteCard($user, $cardId)) {
            return ApiResponse::success('Карта успешно удалена');
        }

        return ApiResponse::error(
            ErrorResponse::NOT_FOUND,
            'Карта не найдена',
            404
        );
    }

    public function setDefaultCard(int $cardId): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }

        try {
            SavedCard::where('user_id', $user->id)->update(['is_default' => false]);

            $card = $this->cardService->getSavedCard($user, $cardId);

            if (!$card) {
                return ApiResponse::error(
                    ErrorResponse::NOT_FOUND,
                    'Карта не найдена',
                    404
                );
            }

            $card->update(['is_default' => true]);

            return ApiResponse::success('Основная карта изменена');
        } catch (\Exception $e) {
            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при изменении основной карты',
                500
            );
        }
    }
}
