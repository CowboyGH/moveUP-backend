<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    use HasFactory;

    protected $table = 'test_attempts';

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
