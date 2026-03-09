<?php

namespace App\Services\WorkoutGeneration\Exercise;

use Illuminate\Support\Collection;

class RestPhaseDetector
{
    protected const BAD_STREAK_FOR_REST = 3;

    /**
     * @param Collection $reactionHistory последние реакции (отсортированы по дате desc)
     * @return array{required: bool, duration: int}|null
     */
    public function shouldRest(Collection $reactionHistory): ?array
    {
        if ($reactionHistory->isEmpty()) {
            return null;
        }

        $consecutiveBad = 0;
        foreach ($reactionHistory as $reaction) {
            if ($reaction->reaction === 'bad') {
                $consecutiveBad++;
            } else {
                break;
            }
        }

        if ($consecutiveBad >= self::BAD_STREAK_FOR_REST) {
            return [
                'required' => true,
                'duration' => $consecutiveBad,
            ];
        }

        return null;
    }
}
