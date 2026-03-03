<?php
// app/Services/WorkoutLoadManagerService.php (обновленный)

namespace App\Services;

use App\Models\User;
use App\Models\UserWorkout;
use App\Models\ExercisePerformance;
use App\Models\WorkoutExercise;
use App\Models\UserExerciseWeight;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkoutLoadManagerService
{
    const LOAD_INCREASE_PERCENT = 10;      // +10% при хороших оценках
    const LOAD_DECREASE_PERCENT = 20;      // -20% при плохих оценках
    const MAX_SETS = 5;                      // Максимальное количество подходов
    const MIN_SETS = 1;                       // Минимальное количество подходов
    const MAX_REPS = 20;                       // Максимальное количество повторений
    const MIN_REPS = 6;                        // Минимальное количество повторений
    const WEIGHT_STEP = 0.5;                   // Шаг изменения веса

    protected ExerciseLoadService $exerciseLoadService;

    public function __construct(ExerciseLoadService $exerciseLoadService)
    {
        $this->exerciseLoadService = $exerciseLoadService;
    }

    /**
     * Завершить тренировку с оценками и обновить параметры упражнений
     */
    public function completeWorkoutWithLoadAdjustment(
        User $user,
        int $workoutId,
        array $reactionsData
    ): array {
        return DB::transaction(function () use ($user, $workoutId, $reactionsData) {
            $userWorkout = UserWorkout::where('user_id', $user->id)
                ->where('workout_id', $workoutId)
                ->where('status', 'started')
                ->firstOrFail();

            $results = [];
            $totalAdjustments = [
                'increases' => 0,
                'decreases' => 0,
                'rest_phases' => 0
            ];

            foreach ($reactionsData as $reactionData) {
                $result = $this->processExerciseReaction(
                    $user,
                    $userWorkout,
                    $reactionData['exercise_id'],
                    $reactionData['reaction'],
                    $reactionData['performance'] ?? []
                );

                if ($result['adjustment_applied']) {
                    if ($result['adjustment_type'] === 'increase') {
                        $totalAdjustments['increases']++;
                    } elseif ($result['adjustment_type'] === 'decrease') {
                        $totalAdjustments['decreases']++;
                    }
                }

                if ($result['rest_phase_activated']) {
                    $totalAdjustments['rest_phases']++;
                }

                $results[] = $result;
            }

            $userWorkout->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            $updatedWeights = $this->getUpdatedExerciseWeights($user);

            return [
                'user_workout' => [
                    'id' => $userWorkout->id,
                    'completed_at' => $userWorkout->completed_at,
                ],
                'exercises_processed' => count($results),
                'adjustments_summary' => $totalAdjustments,
                'exercise_results' => $results,
                'updated_weights' => $updatedWeights,
                'next_workout_recommendations' => $this->generateNextWorkoutRecommendations($results, $totalAdjustments)
            ];
        });
    }

    /**
     * Обработка оценки отдельного упражнения
     */
    private function processExerciseReaction(
        User $user,
        UserWorkout $userWorkout,
        int $exerciseId,
        string $reaction,
        array $performanceData
    ): array {
        // Получаем плановые параметры
        $plannedParams = $this->getPlannedExerciseParameters($user, $userWorkout->workout_id, $exerciseId);

        // Получаем текущий вес пользователя
        $userWeight = $this->getUserExerciseWeight($user->id, $exerciseId);

        // Анализируем историю
        $reactionHistory = $this->exerciseLoadService->getReactionHistory($user->id, $exerciseId);
        $analysis = $this->exerciseLoadService->analyzeReactionPattern($reactionHistory);

        // Определяем корректировку
        $adjustment = $this->determineLoadAdjustment($analysis, $plannedParams, $userWeight);

        // Применяем корректировку веса если нужно
        if ($adjustment['weight_adjusted']) {
            $this->updateUserExerciseWeight(
                $user->id,
                $exerciseId,
                $adjustment['new_weight']
            );
        }

        // Проверяем фазу отдыха
        $restPhase = $this->checkForRestPhase($analysis);

        // Сохраняем производительность
        $performance = $this->saveExercisePerformance(
            $userWorkout->id,
            $exerciseId,
            $reaction,
            $plannedParams,
            $performanceData,
            $userWeight
        );

        return [
            'exercise_id' => $exerciseId,
            'reaction' => $reaction,
            'analysis' => $analysis,
            'adjustment_applied' => $adjustment['weight_adjusted'],
            'adjustment_type' => $adjustment['type'] ?? null,
            'old_weight' => $adjustment['old_weight'] ?? null,
            'new_weight' => $adjustment['new_weight'] ?? null,
            'rest_phase_activated' => !is_null($restPhase),
            'rest_phase_details' => $restPhase,
        ];
    }

    /**
     * Получение плановых параметров (с учетом текущего веса пользователя)
     */
    private function getPlannedExerciseParameters(User $user, int $workoutId, int $exerciseId): array
    {
        $workoutExercise = WorkoutExercise::where('workout_id', $workoutId)
            ->where('exercise_id', $exerciseId)
            ->first();

        $userWeight = $this->getUserExerciseWeight($user->id, $exerciseId);

        return [
            'sets' => $workoutExercise?->sets ?? 3,
            'reps' => $workoutExercise?->reps ?? 12,
            'weight' => $userWeight,
        ];
    }

    /**
     * Получение текущего веса пользователя для упражнения
     */
    private function getUserExerciseWeight(int $userId, int $exerciseId): ?float
    {
        $userWeight = UserExerciseWeight::where('user_id', $userId)
            ->where('exercise_id', $exerciseId)
            ->first();

        return $userWeight?->weight;
    }

    /**
     * Определение корректировки нагрузки
     */
    private function determineLoadAdjustment(array $analysis, array $plannedParams, ?float $currentWeight): array
    {
        $result = [
            'weight_adjusted' => false,
            'type' => null,
            'old_weight' => $currentWeight,
            'new_weight' => $currentWeight,
        ];

        // Если нет веса или он null, ничего не корректируем
        if ($currentWeight === null) {
            return $result;
        }

        // Увеличение при 2+ хороших оценках подряд
        if ($analysis['consecutive_good'] >= 2) {
            $newWeight = $this->calculateIncreasedWeight($currentWeight);
            $result['weight_adjusted'] = true;
            $result['type'] = 'increase';
            $result['new_weight'] = $newWeight;
        }
        // Уменьшение при плохой оценке
        elseif ($analysis['last_reaction'] === 'bad') {
            $newWeight = $this->calculateDecreasedWeight($currentWeight);
            $result['weight_adjusted'] = true;
            $result['type'] = 'decrease';
            $result['new_weight'] = $newWeight;
        }

        return $result;
    }

    /**
     * Расчет увеличенного веса (с шагом 0.5 кг)
     */
    private function calculateIncreasedWeight(float $currentWeight): float
    {
        $increase = $currentWeight * (self::LOAD_INCREASE_PERCENT / 100);
        // Минимальное увеличение - 2.5 кг, но не меньше одного шага
        $minIncrease = max(2.5, self::WEIGHT_STEP);
        $increase = max($minIncrease, $increase);

        $newWeight = $currentWeight + $increase;
        return round($newWeight * 2) / 2; // Округление до 0.5
    }

    /**
     * Расчет уменьшенного веса (с шагом 0.5 кг)
     */
    private function calculateDecreasedWeight(float $currentWeight): float
    {
        $decrease = $currentWeight * (self::LOAD_DECREASE_PERCENT / 100);
        $newWeight = max(1, $currentWeight - $decrease); // Минимум 1 кг
        return round($newWeight * 2) / 2; // Округление до 0.5
    }

    /**
     * Обновление веса пользователя
     */
    private function updateUserExerciseWeight(int $userId, int $exerciseId, float $newWeight): void
    {
        UserExerciseWeight::updateOrCreate(
            [
                'user_id' => $userId,
                'exercise_id' => $exerciseId,
            ],
            [
                'weight' => $newWeight,
                'adjustment_factor' => $newWeight / $this->getUserExerciseWeight($userId, $exerciseId) ?: 1.0,
            ]
        );

        Log::info("Weight updated for user {$userId}, exercise {$exerciseId}: {$newWeight}kg");
    }

    /**
     * Проверка фазы отдыха
     */
    private function checkForRestPhase(array $analysis): ?array
    {
        if ($analysis['consecutive_bad'] >= 3) {
            return [
                'required' => true,
                'duration_days' => $analysis['consecutive_bad'],
                'message' => "Рекомендуется отдых от упражнения на {$analysis['consecutive_bad']} дней",
            ];
        }
        return null;
    }

    /**
     * Сохранение производительности
     */
    private function saveExercisePerformance(
        int $userWorkoutId,
        int $exerciseId,
        string $reaction,
        array $plannedParams,
        array $actualData,
        ?float $userWeight
    ): ExercisePerformance {
        return ExercisePerformance::create([
            'user_workout_id' => $userWorkoutId,
            'exercise_id' => $exerciseId,
            'reaction' => $reaction,
            'sets_completed' => $actualData['sets_completed'] ?? null,
            'reps_completed' => $actualData['reps_completed'] ?? null,
            'weight_used' => $actualData['weight_used'] ?? null,
            'sets_planned' => $plannedParams['sets'],
            'reps_planned' => $plannedParams['reps'],
            'weight_planned' => $userWeight,
            'adjustment_factor' => 1.0,
        ]);
    }

    /**
     * Получение обновленных весов
     */
    private function getUpdatedExerciseWeights(User $user): Collection
    {
        return UserExerciseWeight::where('user_id', $user->id)
            ->with('exercise')
            ->get()
            ->map(function ($weight) {
                return [
                    'exercise_id' => $weight->exercise_id,
                    'exercise_name' => $weight->exercise->title,
                    'current_weight' => $weight->weight,
                    'adjustment_factor' => $weight->adjustment_factor,
                ];
            });
    }

    /**
     * Генерация рекомендаций
     */
    private function generateNextWorkoutRecommendations(array $results, array $adjustments): array
    {
        $recommendations = [];

        if ($adjustments['increases'] > 0) {
            $recommendations[] = "Увеличен вес на {$adjustments['increases']} упражнений(ях)";
        }
        if ($adjustments['decreases'] > 0) {
            $recommendations[] = "Уменьшен вес на {$adjustments['decreases']} упражнений(ях)";
        }
        if ($adjustments['rest_phases'] > 0) {
            $recommendations[] = "Рекомендован отдых для {$adjustments['rest_phases']} упражнений(ях)";
        }

        return $recommendations;
    }
}
