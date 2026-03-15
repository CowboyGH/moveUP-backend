<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExerciseReaction;
use App\Models\ExercisePerformance;
use App\Models\UserExerciseWeight;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExerciseLoadService
{
    const LOAD_INCREASE_PERCENT = 10;
    const LOAD_DECREASE_PERCENT = 20;
    const CONSECUTIVE_GOOD_DAYS = 2;
    const CONSECUTIVE_BAD_DAYS_FOR_REST = 3;

    /**
     * Обработка оценки упражнения
     */
    public function processReaction(
        User $user,
        int $exerciseId,
        string $reaction,
        int $userWorkoutId,
        ?array $performanceData = null
    ): array {
        // Сохраняем оценку
        $exerciseReaction = $this->saveReaction($user, $exerciseId, $reaction, $userWorkoutId);

        // Сохраняем производительность и вес
        if ($performanceData && isset($performanceData['weight_used'])) {
            $this->saveExerciseWeight($user->id, $exerciseId, $performanceData['weight_used']);
        }

        if ($performanceData) {
            $this->saveExercisePerformance($userWorkoutId, $exerciseId, $reaction, $performanceData);
        }

        // Анализируем историю
        $reactionHistory = $this->getReactionHistory($user->id, $exerciseId);
        $analysis = $this->analyzeReactionPattern($reactionHistory);

        // Получаем текущий вес
        $currentWeight = $this->getUserCurrentWeight($user->id, $exerciseId);

        // Применяем корректировки
        $adjustments = $this->applyLoadAdjustments($user, $exerciseId, $analysis, $currentWeight);

        // Проверяем фазу отдыха
        $restPhase = $this->checkForRestPhase($analysis);

        return [
            'reaction' => $exerciseReaction,
            'analysis' => $analysis,
            'adjustments' => $adjustments,
            'rest_phase' => $restPhase,
            'current_weight' => $currentWeight,
            'recommendations' => $this->generateRecommendations($analysis, $adjustments, $restPhase),
        ];
    }

    /**
     * Сохранение веса пользователя
     */
    public function saveExerciseWeight(int $userId, int $exerciseId, float $weight): void
    {
        $roundedWeight = $this->roundToHalf($weight);

        UserExerciseWeight::updateOrCreate(
            [
                'user_id' => $userId,
                'exercise_id' => $exerciseId,
            ],
            [
                'weight' => $roundedWeight,
                'adjustment_factor' => 1.0,
            ]
        );

        Log::info("Saved weight for user {$userId}, exercise {$exerciseId}: {$roundedWeight}kg");
    }

    /**
     * Получение текущего веса пользователя
     */
    public function getUserCurrentWeight(int $userId, int $exerciseId): ?float
    {
        $userWeight = UserExerciseWeight::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->first();

        return $userWeight?->weight;
    }

    /**
     * Округление до 0.5 кг
     */
    private function roundToHalf(float $value): float
    {
        return round($value * 2) / 2;
    }

    /**
     * Сохранение оценки
     */
    private function saveReaction(User $user, int $exerciseId, string $reaction, int $userWorkoutId): ExerciseReaction
    {
        return ExerciseReaction::updateOrCreate(
            [
                'user_id' => $user->id,
                'exercise_id' => $exerciseId,
                'reaction_date' => now()->toDateString(),
            ],
            [
                'user_workout_id' => $userWorkoutId,
                'reaction' => $reaction,
            ]
        );
    }

    /**
     * Сохранение производительности
     */
    private function saveExercisePerformance(int $userWorkoutId, int $exerciseId, string $reaction, array $data): void
    {
        ExercisePerformance::create([
            'user_workout_id' => $userWorkoutId,
            'exercise_id' => $exerciseId,
            'reaction' => $reaction,
            'sets_completed' => $data['sets_completed'] ?? null,
            'reps_completed' => $data['reps_completed'] ?? null,
            'weight_used' => $data['weight_used'] ?? null,
        ]);
    }

    /**
     * Получение истории оценок
     */
    public function getReactionHistory(int $userId, int $exerciseId, int $days = 7): Collection
    {
        return ExerciseReaction::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->where('reaction_date', '>=', now()->subDays($days))
            ->orderBy('reaction_date', 'desc')
            ->get();
    }

    /**
     * Анализ паттерна оценок - ИСПРАВЛЕННАЯ ВЕРСИЯ
     */
    public function analyzeReactionPattern(Collection $reactions): array
    {
        if ($reactions->isEmpty()) {
            return [
                'pattern' => 'no_data',
                'consecutive_good' => 0,
                'consecutive_bad' => 0,
                'last_reaction' => null,
                'trend' => 'neutral',
                'stats' => [
                    'good' => 0,
                    'normal' => 0,
                    'bad' => 0,
                    'total' => 0,
                ],
            ];
        }

        // Сортируем по возрастанию даты для правильного подсчета последовательности
        $sortedReactions = $reactions->sortBy('reaction_date')->values();

        $consecutiveGood = 0;
        $consecutiveBad = 0;
        $goodCount = 0;
        $badCount = 0;
        $normalCount = 0;

        $currentStreak = 0;
        $currentStreakType = null;
        $previousDate = null;

        foreach ($sortedReactions as $reaction) {
            $currentDate = $reaction->reaction_date;

            // Подсчет общего количества
            if ($reaction->reaction === 'good') {
                $goodCount++;
            } elseif ($reaction->reaction === 'bad') {
                $badCount++;
            } else {
                $normalCount++;
            }

            // Проверяем последовательность
            if ($previousDate) {
                $diff = $previousDate->diffInDays($currentDate);

                // Если даты идут подряд (разница в 1 день)
                if ($diff == 1) {
                    // Если та же оценка, продолжаем streak
                    if ($reaction->reaction === $currentStreakType) {
                        $currentStreak++;
                    } else {
                        // Сменилась оценка - начинаем новый streak
                        $currentStreak = 1;
                        $currentStreakType = $reaction->reaction;
                    }
                } else {
                    // Есть пропуск в днях - начинаем новый streak
                    $currentStreak = 1;
                    $currentStreakType = $reaction->reaction;
                }
            } else {
                // Первая реакция
                $currentStreak = 1;
                $currentStreakType = $reaction->reaction;
            }

            // Обновляем максимальные значения streak
            if ($currentStreakType === 'good' && $currentStreak > $consecutiveGood) {
                $consecutiveGood = $currentStreak;
            }
            if ($currentStreakType === 'bad' && $currentStreak > $consecutiveBad) {
                $consecutiveBad = $currentStreak;
            }

            $previousDate = $currentDate;
        }

        // Определяем тренд на основе последней реакции и streak
        $lastReaction = $reactions->first()->reaction;
        $trend = 'neutral';

        if ($lastReaction === 'good' && $consecutiveGood >= self::CONSECUTIVE_GOOD_DAYS) {
            $trend = 'positive_streak';
        } elseif ($lastReaction === 'bad') {
            if ($consecutiveBad >= self::CONSECUTIVE_BAD_DAYS_FOR_REST) {
                $trend = 'negative_critical';
            } elseif ($consecutiveBad > 0) {
                $trend = 'negative';
            }
        }

        return [
            'pattern' => $this->determinePattern($goodCount, $badCount, $normalCount, $reactions->count()),
            'consecutive_good' => $consecutiveGood,
            'consecutive_bad' => $consecutiveBad,
            'last_reaction' => $lastReaction,
            'trend' => $trend,
            'stats' => [
                'good' => $goodCount,
                'normal' => $normalCount,
                'bad' => $badCount,
                'total' => $reactions->count(),
            ],
        ];
    }

    /**
     * Определение паттерна
     */
    private function determinePattern(int $good, int $bad, int $normal, int $total): string
    {
        if ($total === 0) return 'no_data';

        $goodRatio = $good / $total;
        $badRatio = $bad / $total;

        if ($goodRatio >= 0.7) return 'consistently_good';
        if ($badRatio >= 0.7) return 'consistently_bad';
        if ($goodRatio >= 0.5) return 'mostly_good';
        if ($badRatio >= 0.5) return 'mostly_bad';
        return 'mixed';
    }

    /**
     * Применение корректировок нагрузки - ИСПРАВЛЕНО
     */
    private function applyLoadAdjustments(User $user, int $exerciseId, array $analysis, ?float $currentWeight): array
    {
        $adjustments = [
            'applied' => false,
            'type' => null,
            'percent' => 0,
            'old_weight' => $currentWeight,
            'new_weight' => $currentWeight,
            'message' => null,
        ];

        if ($currentWeight === null) {
            return $adjustments;
        }

        // Увеличение при 2+ хороших оценках подряд
        if ($analysis['trend'] === 'positive_streak') {
            $newWeight = $this->calculateIncreasedWeight($currentWeight);

            $adjustments = [
                'applied' => true,
                'type' => 'increase',
                'percent' => self::LOAD_INCREASE_PERCENT,
                'old_weight' => $currentWeight,
                'new_weight' => $newWeight,
                'message' => "Увеличьте вес с {$currentWeight}кг до {$newWeight}кг",
            ];

            $this->updateUserExerciseWeight($user->id, $exerciseId, $newWeight);
        }
        // Уменьшение при плохой оценке
        elseif ($analysis['last_reaction'] === 'bad') {
            $newWeight = $this->calculateDecreasedWeight($currentWeight);

            $adjustments = [
                'applied' => true,
                'type' => 'decrease',
                'percent' => self::LOAD_DECREASE_PERCENT,
                'old_weight' => $currentWeight,
                'new_weight' => $newWeight,
                'message' => "Снизьте вес с {$currentWeight}кг до {$newWeight}кг",
            ];

            $this->updateUserExerciseWeight($user->id, $exerciseId, $newWeight);
        }

        return $adjustments;
    }

    /**
     * Расчет увеличенного веса
     */
    private function calculateIncreasedWeight(float $currentWeight): float
    {
        $increase = $currentWeight * (self::LOAD_INCREASE_PERCENT / 100);
        $increase = max(2.5, $increase);
        $newWeight = $currentWeight + $increase;
        return $this->roundToHalf($newWeight);
    }

    /**
     * Расчет уменьшенного веса
     */
    private function calculateDecreasedWeight(float $currentWeight): float
    {
        $decrease = $currentWeight * (self::LOAD_DECREASE_PERCENT / 100);
        $newWeight = max(1, $currentWeight - $decrease);
        return $this->roundToHalf($newWeight);
    }

    /**
     * Обновление веса пользователя
     */
    private function updateUserExerciseWeight(int $userId, int $exerciseId, float $newWeight): void
    {
        $userWeight = UserExerciseWeight::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->first();

        if ($userWeight) {
            $oldWeight = $userWeight->weight;
            $userWeight->update(['weight' => $newWeight]);

            Log::info("Weight updated for user {$userId}, exercise {$exerciseId}: {$oldWeight}kg -> {$newWeight}kg");
        }
    }

    /**
     * Проверка фазы отдыха
     */
    private function checkForRestPhase(array $analysis): ?array
    {
        if ($analysis['consecutive_bad'] >= self::CONSECUTIVE_BAD_DAYS_FOR_REST) {
            return [
                'required' => true,
                'duration_days' => $analysis['consecutive_bad'],
                'message' => "Рекомендуется фаза отдыха от этого упражнения на {$analysis['consecutive_bad']} дня",
            ];
        }
        return null;
    }

    /**
     * Генерация рекомендаций
     */
    private function generateRecommendations(array $analysis, array $adjustments, ?array $restPhase): array
    {
        $recommendations = [];

        if ($restPhase) {
            $recommendations[] = $restPhase['message'];
        } elseif ($adjustments['applied']) {
            $recommendations[] = $adjustments['message'];
        }

        return $recommendations;
    }

    /**
     * Получение рекомендации по нагрузке
     */
    public function getLoadRecommendation(User $user, int $exerciseId): array
    {
        $history = $this->getReactionHistory($user->id, $exerciseId);
        $analysis = $this->analyzeReactionPattern($history);
        $currentWeight = $this->getUserCurrentWeight($user->id, $exerciseId);

        $workoutExercise = \App\Models\WorkoutExercise::where('exercise_id', $exerciseId)->first();

        $currentLoad = [
            'weight' => $currentWeight ?? 0,
            'sets' => $workoutExercise->sets ?? 3,
            'reps' => $workoutExercise->reps ?? 12,
            'difficulty' => 'medium',
        ];

        $recommendedLoad = $currentLoad;
        $explanation = 'Нагрузка оптимальна';

        // ИСПРАВЛЕНО: используем анализ из history, а не делаем отдельный запрос
        if ($analysis['trend'] === 'positive_streak' && $currentWeight) {
            $recommendedLoad['weight'] = $this->calculateIncreasedWeight($currentWeight);
            $explanation = 'Рекомендуется увеличить вес на ' . self::LOAD_INCREASE_PERCENT . '% (2+ хороших оценок подряд)';
        } elseif ($analysis['last_reaction'] === 'bad' && $currentWeight) {
            $recommendedLoad['weight'] = $this->calculateDecreasedWeight($currentWeight);
            $explanation = 'Рекомендуется снизить вес на ' . self::LOAD_DECREASE_PERCENT . '% (последняя оценка плохая)';
        }

        return [
            'exercise_id' => $exerciseId,
            'current_load' => $currentLoad,
            'recommended_load' => $recommendedLoad,
            'explanation' => $explanation,
            'analysis' => $analysis,
            'rest_phase_needed' => $analysis['trend'] === 'negative_critical',
        ];
    }
}
