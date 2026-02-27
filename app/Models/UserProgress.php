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
    ];

    /**
     * Получить дату последней тренировки пользователя
     */
    public function getLastWorkoutDateAttribute()
    {
        return $this->user->userWorkouts()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->value('completed_at');
    }

    /**
     * Проверка, была ли тренировка сегодня
     */
    public function hasWorkoutToday(): bool
    {
        return $this->user->userWorkouts()
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->exists();
    }

    /**
     * Обновление streak_days после тренировки
     */
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

    /**
     * Проверка, можно ли перейти на следующую фазу
     */
    public function canAdvanceToNextPhase(): bool
    {
        $daysPassed = now()->diffInDays($this->created_at);

        return $daysPassed >= $this->phase->duration_days ||
            $this->completed_workouts >= $this->phase->min_workouts;
    }

    /**
     * Сброс прогресса для новой фазы
     */
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
