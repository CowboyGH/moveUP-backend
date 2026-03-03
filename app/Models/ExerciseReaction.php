<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'user_workout_id',
        'reaction',
        'reaction_date',
    ];

    protected $casts = [
        'reaction_date' => 'date',
    ];

    const REACTION_GOOD = 'good';
    const REACTION_NORMAL = 'normal';
    const REACTION_BAD = 'bad';

    public static function getReactions(): array
    {
        return [
            self::REACTION_GOOD,
            self::REACTION_NORMAL,
            self::REACTION_BAD,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function userWorkout(): BelongsTo
    {
        return $this->belongsTo(UserWorkout::class);
    }
}
