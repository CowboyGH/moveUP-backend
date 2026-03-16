<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Models\User;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Support\Facades\Log;

class RegenerationController extends BaseWorkoutController
{
    public function checkAndRegenerateWorkouts(User $user, int $exerciseId): void
    {
        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            return;
        }
        // Проверяем, есть ли будущие тренировки с этим упражнением
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
        if ($hasFutureExercises) {
            Log::info("Фаза отдыха для упражнения {$exerciseId}. Перегенерация тренировок для пользователя {$user->id}");
            $user->userWorkouts()
                ->where('status', 'started')
                ->whereNull('started_at')
                ->delete();

            $workoutGenerator = app(WorkoutGeneratorService::class);
            $workouts = $workoutGenerator->generateForPhase($user, $currentProgress->phase);

            if ($workouts->isNotEmpty()) {
                $workoutGenerator->assignWorkoutsToUser($user, $workouts);
            }
        }
    }
}
