<?php

namespace App\Services\WorkoutGeneration\Selector;

use Illuminate\Support\Collection;

class GoalBasedWorkoutSelector implements WorkoutSelectorInterface
{
    protected const GOAL_WORKOUT_TYPES = [
        1 => ['strength', 'power'],           // Рост силовых
        2 => ['strength', 'hypertrophy'],      // Рост массы
        3 => ['cardio', 'hiit', 'circuit'],    // Жиросжигание
        4 => ['general', 'functional'],        // Общее укрепление
    ];

    public function select(Collection $workouts, int $needed, ?int $goalId): Collection
    {
        if ($workouts->count() <= $needed) {
            return $workouts;
        }

        if (!$goalId || empty(self::GOAL_WORKOUT_TYPES[$goalId])) {
            return $workouts->random($needed);
        }

        $preferredTypes = self::GOAL_WORKOUT_TYPES[$goalId];
        $preferred = $workouts->filter(fn($w) => in_array($w->type ?? 'general', $preferredTypes));
        $other = $workouts->diff($preferred);

        $preferredNeeded = min((int) ceil($needed * 0.7), $preferred->count());
        $otherNeeded = $needed - $preferredNeeded;

        $selected = collect();
        if ($preferredNeeded > 0 && $preferred->isNotEmpty()) {
            $selected = $selected->merge($preferred->random($preferredNeeded));
        }
        if ($otherNeeded > 0 && $other->isNotEmpty()) {
            $selected = $selected->merge($other->random($otherNeeded));
        }
        return $selected;
    }
}
