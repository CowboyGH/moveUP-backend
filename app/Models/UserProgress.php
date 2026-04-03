<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phase_id',
        'streak_days',
        'completed_workouts',
        'weekly_workout_goal',
    ];

    public function getLastWorkoutDateAttribute()
    {
        return $this->user->userWorkouts()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->value('completed_at');
    }

    public function hasWorkoutToday(): bool
    {
        return $this->user->userWorkouts()
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->exists();
    }

    public function updateStreakAfterWorkout(): void
    {
        $lastWorkout = $this->user->userWorkouts()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (!$lastWorkout) {
            $this->streak_days = 1;
        } else {
            $lastWorkoutDate = $lastWorkout->completed_at->startOfDay();
            $today = now()->startOfDay();

            if ($lastWorkoutDate->diffInDays($today) == 1) {
                $this->streak_days++;
            } else if ($lastWorkoutDate->diffInDays($today) > 1) {
                $this->streak_days = 1;
            }
        }

        $this->completed_workouts++;
        $this->save();
    }

    public function canAdvanceToNextPhase(): bool
    {
        $daysPassed = now()->diffInDays($this->created_at);
        $phaseDuration = $this->phase->duration_days;

        $weeksPassed = $daysPassed / 7;
        $expectedWorkouts = ceil($weeksPassed * $this->weekly_workout_goal);

        $minRequiredWorkouts = ceil($expectedWorkouts * 0.5);


        $enoughTimePassed = $daysPassed >= $phaseDuration;
        $enoughWorkoutsDone = $this->completed_workouts >= $expectedWorkouts;
        $minWorkoutsDone = $this->completed_workouts >= $minRequiredWorkouts;

        return $enoughWorkoutsDone || ($enoughTimePassed && $minWorkoutsDone);
    }

    public function resetForNewPhase(Phase $newPhase): self
    {
        $this->phase_id = $newPhase->id;
        $this->streak_days = 0;
        $this->completed_workouts = 0;
        $this->created_at = now();
        $this->save();

        return $this;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }
}
