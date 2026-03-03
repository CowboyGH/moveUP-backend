<?php
// app/Models/ExercisePerformance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExercisePerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_workout_id',
        'exercise_id',
        'reaction',
        'sets_completed',
        'reps_completed',
        'weight_used',
        'sets_planned',
        'reps_planned',
        'weight_planned',
        'adjustment_factor',
    ];

    protected $casts = [
        'adjustment_factor' => 'decimal:2',
    ];

    /**
     * Get the user through user_workout
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserWorkout::class,
            'id',
            'id',
            'user_workout_id',
            'user_id'
        );
    }

    public function userWorkout(): BelongsTo
    {
        return $this->belongsTo(UserWorkout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
