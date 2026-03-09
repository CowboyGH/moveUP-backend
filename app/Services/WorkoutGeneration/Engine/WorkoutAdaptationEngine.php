<?php

namespace App\Services\WorkoutGeneration\Engine;

use App\Models\Workout;
use App\Services\WorkoutGeneration\Context\UserContext;
use App\Services\WorkoutGeneration\Exercise\ExerciseAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WorkoutAdaptationEngine
{
    protected ExerciseAdapter $exerciseAdapter;

    public function __construct(ExerciseAdapter $exerciseAdapter)
    {
        $this->exerciseAdapter = $exerciseAdapter;
    }

    /**
     * Адаптирует тренировку: обрабатывает каждое упражнение.
     * Возвращает клон тренировки с адаптированными упражнениями.
     */
    public function adapt(Workout $workout, UserContext $context): Workout
    {
        $adaptedWorkout = clone $workout;
        $adaptedExercises = collect();

        foreach ($workout->exercises as $exercise) {
            $adapted = $this->exerciseAdapter->adapt($exercise, $context);
            if ($adapted !== null) {
                $adaptedExercises->push($adapted);
            }
        }

        $adaptedWorkout->exercises = $adaptedExercises;
        $adaptedWorkout->adaptation_notes = $this->generateNotes($context);

        return $adaptedWorkout;
    }

    protected function generateNotes(UserContext $context): array
    {
        $notes = [];
        if ($context->parameters) {
            $notes[] = "Тренировка подобрана с учетом вашего уровня подготовки";
            if ($context->parameters->goal) {
                $notes[] = "Акцент на достижение цели: {$context->parameters->goal->name}";
            }
        }
        if (!empty($context->testResults)) {
            $notes[] = "Учтены результаты ваших тестов";
        }
        if ($context->currentProgress && $context->currentProgress->completed_workouts > 0) {
            $notes[] = "Учтён ваш прогресс в текущей фазе";
        }
        return $notes;
    }
}
