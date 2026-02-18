<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_holder',
        'card_number_hash',
        'card_last_four',
        'expiry_month',
        'expiry_year',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'card_number_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Хеширование номера карты
     */
    public static function hashCardNumber(string $cardNumber): string
    {
        return bcrypt($cardNumber);
    }

    /**
     * Проверка номера карты
     */
    public function verifyCardNumber(string $cardNumber): bool
    {
        return password_verify($cardNumber, $this->card_number_hash);
    }

    /**
     * Форматирование срока действия для отображения
     */
    public function getExpiryFormattedAttribute(): string
    {
        return $this->expiry_month . '/' . $this->expiry_year;
    }

}
