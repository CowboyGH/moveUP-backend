<?php

namespace App\Services\WorkoutGeneration\Exercise;

use Illuminate\Support\Collection;

class ExerciseReplacementDecider
{
    /**
     * @param Collection $reactionHistory
     */
    public function shouldReplace(Collection $reactionHistory): bool
    {
        if ($reactionHistory->isEmpty()) {
            return false;
        }

        $badCount = $reactionHistory->where('reaction', 'bad')->count();
        $total = $reactionHistory->count();

        return $total > 2 && ($badCount / $total) > 0.5;
    }
}
