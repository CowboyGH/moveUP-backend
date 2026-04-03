<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExerciseWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'weight',
        'adjustment_factor',
    ];

    protected $casts = [
        'weight' => 'decimal:1',
        'adjustment_factor' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public static function roundWeight(float $weight): float
    {
        return round($weight * 2) / 2;
    }
}
