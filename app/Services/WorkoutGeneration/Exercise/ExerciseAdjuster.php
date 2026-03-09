<?php

namespace App\Services\WorkoutGeneration\Exercise;

use App\Models\Exercise;
use App\Services\WorkoutGeneration\Context\UserContext;
use App\Services\ExerciseLoadService;

//Это корректировка параметров с учётом реакций и пульса
class ExerciseAdjuster
{
    protected const GOOD_STREAK_THRESHOLD = 2;
    protected ExerciseLoadService $loadService;

    public function __construct(ExerciseLoadService $loadService)
    {
        $this->loadService = $loadService;
    }

    public function adjust(Exercise $exercise, array $baseParams, UserContext $context): array
    {
        $recommendation = $this->loadService->getLoadRecommendation($context->user, $exercise->id);
        $analysis = $recommendation['analysis'] ?? [];

        $params = $baseParams;

        if (!empty($analysis)) {
            // Плохая последняя реакция – снижение
            if (($analysis['last_reaction'] ?? null) === 'bad') {
                $params['sets'] = max(1, $params['sets'] - 1);
                $params['reps'] = $this->decreaseReps($params['reps']);
            }

            // Хороший streak – увеличение
            if (($analysis['trend'] ?? null) === 'positive_streak' &&
                ($analysis['consecutive_good'] ?? 0) >= self::GOOD_STREAK_THRESHOLD) {
                $params['sets'] = min(5, $params['sets'] + 1);
                $params['reps'] = $this->increaseReps($params['reps']);
            }
        }

        // Высокий пульс после теста – снижение
        if ($context->lastTestPulse && $context->lastTestPulse > 150) {
            $params['sets'] = max(1, $params['sets'] - 1);
            $params['reps'] = $this->decreaseReps($params['reps']);
        }

        return $params;
    }

    protected function decreaseReps(string $reps): string
    {
        if (str_contains($reps, '-')) {
            [$min, $max] = explode('-', $reps);
            $min = max(1, (int)$min - 2);
            $max = max($min, (int)$max - 2);
            return $min . '-' . $max;
        }
        return (string) max(1, (int)$reps - 2);
    }

    protected function increaseReps(string $reps): string
    {
        if (str_contains($reps, '-')) {
            [$min, $max] = explode('-', $reps);
            $min = (int)$min + 2;
            $max = (int)$max + 2;
            return $min . '-' . $max;
        }
        return (string) ((int)$reps + 2);
    }
}
