<?php

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
    ];

    protected $casts = [
        'reaction' => 'string',
    ];

    public function userWorkout(): BelongsTo
    {
        return $this->belongsTo(UserWorkout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
