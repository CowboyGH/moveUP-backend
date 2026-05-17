<?php

namespace App\Services\Workouts\Execution;

use App\Models\User;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Support\Facades\Log;

class RegenerationService
{
    public function __construct(
        private readonly WorkoutGeneratorService $workoutGenerator
    ) {}

    public function checkAndRegenerateWorkouts(User $user, int $exerciseId): void
    {
        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            return;
        }

        $activeWorkouts = $user->userWorkouts()
            ->with('workout.exercises')
            ->where('status', 'started')
            ->whereNull('started_at')
            ->get();

        $hasFutureExercises = false;
        foreach ($activeWorkouts as $userWorkout) {
            foreach ($userWorkout->workout->exercises as $exercise) {
                if ($exercise->id == $exerciseId) {
                    $hasFutureExercises = true;
                    break 2;
                }
            }
        }

        if (!$hasFutureExercises) {
            return;
        }

        Log::info('Rest phase requires workout regeneration', [
            'user_id' => $user->id,
            'exercise_id' => $exerciseId,
        ]);

        $user->userWorkouts()
            ->where('status', 'started')
            ->whereNull('started_at')
            ->delete();

        $workouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);

        if ($workouts->isNotEmpty()) {
            $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);
        }
    }
}
