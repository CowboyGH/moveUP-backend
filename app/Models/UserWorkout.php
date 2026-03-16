<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserWorkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_id',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => 'string',
    ];

    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_STARTED = 'started';
    public const STATUS_COMPLETED = 'completed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercisePerformances(): HasMany
    {
        return $this->hasMany(ExercisePerformance::class);
    }

    public function userWarmupPerformances(): HasMany
    {
        return $this->hasMany(UserWarmupPerformance::class);
    }

    public function canBeStarted(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }
    public function isStarted(): bool
    {
        return $this->status === self::STATUS_STARTED;
    }
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
