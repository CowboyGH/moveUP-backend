<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class GuestDataService
{
    private const REDIS_PREFIX = 'guest:user_parameters:';
    private const TTL_SECONDS = 60 * 60 * 24 * 30;

    /**
     * Получить или создать guest ID для неавторизованного пользователя
     */
    public function getGuestId(Request $request): string
    {
        $guestId = $request->header('X-Guest-ID') ?? $request->cookie('guest_id');
        return $guestId ?? Str::uuid()->toString();
    }

    /**
     * Получить данные гостя из Redis
     */
    public function getGuestData(string $guestId): array
    {
        $data = Redis::get(self::REDIS_PREFIX . $guestId);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * Сохранить данные гостя в Redis
     */
    public function saveGuestData(string $guestId, array $data): void
    {
        $key = self::REDIS_PREFIX . $guestId;

        // Добавляем метки времени
        $data['updated_at'] = now()->toDateTimeString();
        if (!isset($data['created_at'])) {
            $data['created_at'] = now()->toDateTimeString();
        }

        Redis::setex($key, self::TTL_SECONDS, json_encode($data));
    }

    /**
     * Обновить конкретное поле в данных гостя
     */
    public function updateGuestField(string $guestId, string $field, $value): array
    {
        $data = $this->getGuestData($guestId);
        $data[$field] = $value;
        $this->saveGuestData($guestId, $data);

        return $data;
    }

    /**
     * Обновить несколько полей
     */
    public function updateGuestFields(string $guestId, array $fields): array
    {
        $data = $this->getGuestData($guestId);
        $data = array_merge($data, $fields);
        $this->saveGuestData($guestId, $data);

        return $data;
    }

    /**
     * Очистить данные гостя из Redis
     */
    public function clearGuestData(string $guestId): void
    {
        Redis::del(self::REDIS_PREFIX . $guestId);
    }

    /**
     * Проверить, существуют ли данные гостя
     */
    public function hasGuestData(string $guestId): bool
    {
        return Redis::exists(self::REDIS_PREFIX . $guestId) === 1;
    }
}
