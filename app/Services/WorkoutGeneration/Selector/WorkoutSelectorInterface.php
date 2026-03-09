<?php

namespace App\Services\WorkoutGeneration\Selector;

use Illuminate\Support\Collection;

interface WorkoutSelectorInterface
{
    /**
     * @param Collection $workouts
     * @param int $needed
     * @param int|null $goalId
     * @return Collection
     */
    public function select(Collection $workouts, int $needed, ?int $goalId): Collection;
}
