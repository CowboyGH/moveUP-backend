<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWarmupPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'warmup_id',
        'user_workout_id',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function warmup(): BelongsTo
    {
        return $this->belongsTo(Warmup::class);
    }

    public function userWorkout(): BelongsTo
    {
        return $this->belongsTo(UserWorkout::class);
    }
}
