<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'testing_id',
        'started_at',
        'completed_at',
        'pulse',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function testing(): BelongsTo
    {
        return $this->belongsTo(Testing::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * Получить пользователя через первый результат (все результаты одной попытки принадлежат одному пользователю).
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            TestResult::class,
            'test_attempt_id',
            'id',
            'id',
            'user_id'
        );
    }
}
