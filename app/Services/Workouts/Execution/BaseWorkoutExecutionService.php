<?php

namespace App\Services\Workouts\Execution;

use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use App\Services\PhaseService;
use Illuminate\Support\Collection;

abstract class BaseWorkoutExecutionService
{
    public function __construct(
        protected readonly ExerciseLoadService $exerciseLoadService,
        protected readonly PhaseService $phaseService
    ) {}

    protected function getSortedExercises(UserWorkout $userWorkout): Collection
    {
        $workout = $userWorkout->workout()->with('exercises')->first();

        return $workout->exercises->sortBy('pivot.order_number')->values();
    }

    protected function getSortedWarmups(UserWorkout $userWorkout): Collection
    {
        $workout = $userWorkout->workout()->with('warmups')->first();

        return $workout->warmups->sortBy('pivot.order_number')->values();
    }
}
