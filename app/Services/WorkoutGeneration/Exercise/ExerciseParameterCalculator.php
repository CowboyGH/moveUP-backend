<?php

namespace App\Services\WorkoutGeneration\Exercise;

use App\Models\Exercise;
use App\Services\WorkoutGeneration\Context\UserContext;
//Это так скажем калькулятор, который рассчитывает параметры для упражнений в тренировках!
class ExerciseParameterCalculator
{
    protected const TEST_RESULT_LOAD_FACTORS = [
        1 => 0.7,
        2 => 0.85,
        3 => 1.0,
        4 => 1.15,
    ];

    public function calculate(Exercise $exercise, UserContext $context): array
    {
        $baseSets = $exercise->pivot->sets ?? 3;
        $baseReps = $exercise->pivot->reps ?? '12';

        // Уровень подготовки
        $levelId = $context->parameters->level_id ?? 2;
        $sets = match ($levelId) {
            1 => max(2, $baseSets - 1),
            2 => $baseSets,
            3 => min(5, $baseSets + 1),
            default => $baseSets,
        };

        $reps = $baseReps;
        if (str_contains($baseReps, '-')) {
            [$min, $max] = explode('-', $baseReps);
            $reps = match ($levelId) {
                1 => (max(1, (int)$min - 2)) . '-' . (max(1, (int)$max - 2)),
                3 => ((int)$min + 2) . '-' . ((int)$max + 2),
                default => $baseReps,
            };
        }

        // Коэффициент теста
        $testFactor = $this->getTestLoadFactor($exercise->id, $context);
        if ($testFactor !== 1.0) {
            $sets = (int) round($sets * $testFactor);
            $sets = max(1, min(5, $sets));

            if (str_contains($reps, '-')) {
                [$min, $max] = explode('-', $reps);
                $min = (int) round($min * $testFactor);
                $max = (int) round($max * $testFactor);
                $min = max(1, $min);
                $max = max($min, $max);
                $reps = $min . '-' . $max;
            } else {
                $reps = (int) round((int)$reps * $testFactor);
                $reps = max(1, $reps);
            }
        }

        // Прогресс в фазе (>50% тренировок выполнено)
        if ($context->currentProgress) {
            $completed = $context->currentProgress->completed_workouts;
            $totalInPhase = $context->currentProgress->phase->duration_days ?? 7;
            $progressRatio = $totalInPhase > 0 ? $completed / $totalInPhase : 0;

            if ($progressRatio > 0.5) {
                $sets = min(5, $sets + 1);
                if (str_contains($reps, '-')) {
                    [$min, $max] = explode('-', $reps);
                    $min += 2;
                    $max += 2;
                    $reps = $min . '-' . $max;
                } else {
                    $reps = (int)$reps + 2;
                }
            }
        }

        return ['sets' => $sets, 'reps' => $reps];
    }

    protected function getTestLoadFactor(int $exerciseId, UserContext $context): float
    {
        $resultValue = $context->testResults[$exerciseId] ?? null;
        if ($resultValue && isset(self::TEST_RESULT_LOAD_FACTORS[$resultValue])) {
            return self::TEST_RESULT_LOAD_FACTORS[$resultValue];
        }
        return 1.0;
    }
}
