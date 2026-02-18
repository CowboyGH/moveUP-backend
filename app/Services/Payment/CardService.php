<?php
namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\SavedCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardService
{
    /**
     * Создание или получение существующей карты
     */
    public function createOrGetCard(User $user, array $cardData, bool $saveCard = false): ?SavedCard
    {
        if (!$saveCard) {
            return null;
        }
        try {
            if (empty($cardData['card_number'])) {
                return null;
            }

            $cardHash = bcrypt($cardData['card_number']);

            $existingCard = SavedCard::where('user_id', $user->id)->where('card_number_hash', $cardHash)->first();

            if ($existingCard) {
                if (!$existingCard->is_default && !SavedCard::where('user_id', $user->id)->where('is_default', true)->exists()) {
                    $existingCard->update(['is_default' => true]);
                }

                return $existingCard;
            }

            $isFirstCard = !SavedCard::where('user_id', $user->id)->exists();

            $savedCard = SavedCard::create([
                'user_id' => $user->id,
                'card_holder' => $cardData['card_holder'],
                'card_number_hash' => $cardHash,
                'card_last_four' => substr($cardData['card_number'], -4),
                'expiry_month' => $cardData['expiry_month'],
                'expiry_year' => $cardData['expiry_year'],
                'is_default' => $isFirstCard,
            ]);
            return $savedCard;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Сохранение карты
     */
    public function saveCard(User $user, array $cardData): ?SavedCard
    {
        return $this->createOrGetCard($user, $cardData, true);
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
        return SavedCard::where('user_id', $user->id)->where('is_default', true)->first();
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

        return DB::transaction(function () use ($user, $card) {
            Payment::where('saved_card_id', $card->id)
                ->update(['saved_card_id' => null]);

            $wasDefault = $card->is_default;
            $deleted = $card->delete();

            // Если удалили основную карту и есть другие карты, делаем основную первую попавшуюся
            if ($wasDefault && $deleted) {
                $newDefault = SavedCard::where('user_id', $user->id)->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            return $deleted;
        });
    }
    /**
     * Генерация маскированного номера для отображения
     */
    public function generateMaskedNumber(string $lastFour): string
    {
        return '**** **** **** ' . $lastFour;
    }
}
