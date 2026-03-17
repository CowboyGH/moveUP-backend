<?php

namespace App\Services\WorkoutGeneration;

use App\Models\User;
use App\Models\Phase;
use App\Models\Workout;
use App\Services\ExerciseLoadService;
use App\Services\WorkoutGeneration\Context\UserContext;
use App\Services\WorkoutGeneration\Selector\WorkoutSelectorInterface;
use App\Services\WorkoutGeneration\Engine\WorkoutAdaptationEngine;
use App\Services\WorkoutGeneration\Assigner\WorkoutAssigner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WorkoutGeneratorService
{
    protected ExerciseLoadService $exerciseLoadService;
    protected WorkoutSelectorInterface $workoutSelector;
    protected WorkoutAdaptationEngine $adaptationEngine;
    protected WorkoutAssigner $assigner;

    public function __construct(
        ExerciseLoadService $exerciseLoadService,
        WorkoutSelectorInterface $workoutSelector,
        WorkoutAdaptationEngine $adaptationEngine,
        WorkoutAssigner $assigner
    ) {
        $this->exerciseLoadService = $exerciseLoadService;
        $this->workoutSelector = $workoutSelector;
        $this->adaptationEngine = $adaptationEngine;
        $this->assigner = $assigner;
    }

    /**
     * Генерирует тренировки для пользователя в указанной фазе (без сохранения).
     */
    public function generateForPhase(User $user, Phase $phase): Collection
    {
        Log::info("🎯 Генерация для фазы", [
            'user_id' => $user->id,
            'phase_id' => $phase->id,
            'goal_id' => $user->userParameters?->goal_id,
            'level_id' => $user->userParameters?->level_id
        ]);

        $context = new UserContext($user, $this->exerciseLoadService);

        $allWorkouts = Workout::where('phase_id', $phase->id)
            ->where('is_active', 1)
            ->with(['exercises', 'warmups'])
            ->get();

        if ($allWorkouts->isEmpty()) {
            Log::warning("Нет активных тренировок для фазы ID: {$phase->id}");
            return collect();
        }

        $needed = $phase->duration_days;
        $goalId = $context->parameters?->goal_id;
        $selectedWorkouts = $this->workoutSelector->select($allWorkouts, $needed, $goalId);

        $adaptedWorkouts = $selectedWorkouts->map(
            fn($workout) => $this->adaptationEngine->adapt($workout, $context)
        );

        return $adaptedWorkouts;
    }

    /**
     * Назначает сгенерированные тренировки пользователю.
     */
    public function assignWorkoutsToUser(User $user, Collection $workouts): void
    {
        $this->assigner->assign($user, $workouts);
    }
}
