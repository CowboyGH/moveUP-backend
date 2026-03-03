<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Phase;
use App\Models\User;
use App\Models\UserParameter;
use App\Models\UserWorkout;
use App\Models\Workout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkoutGeneratorService
{
    protected User $user;
    protected ?UserParameter $userParameters;
    protected Phase $phase;

    // Маппинг целей на типы тренировок (нужно будет добавить поле type в workouts)
    protected const GOAL_WORKOUT_TYPES = [
        1 => ['strength', 'power'],           // Рост силовых показателей
        2 => ['strength', 'hypertrophy'],      // Рост мышечной массы
        3 => ['cardio', 'hiit', 'circuit'],    // Жиросжигание
        4 => ['general', 'functional'],        // Общее укрепление организма
    ];

    public function generateForPhase(User $user, Phase $phase): Collection
    {
        $this->user = $user;
        $this->phase = $phase;
        $this->userParameters = $user->userParameters;

        // Получаем все тренировки фазы
        $allWorkouts = Workout::where('phase_id', $phase->id)
            ->where('is_active', 1)
            ->with(['exercises', 'warmups'])
            ->get();

        if ($allWorkouts->isEmpty()) {
            Log::warning("Нет активных тренировок для фазы ID: {$phase->id}");
            return collect();
        }

        // Выбираем тренировки на период фазы
        $selectedWorkouts = $this->selectWorkoutsForPeriod($allWorkouts);

        // Адаптируем выбранные тренировки
        $adaptedWorkouts = $this->adaptWorkouts($selectedWorkouts);

        return $adaptedWorkouts;
    }

    /**
     * Выбор тренировок на период фазы
     */
    protected function selectWorkoutsForPeriod(Collection $workouts): Collection
    {
        // Количество тренировок = количество дней в фазе
        $workoutsNeeded = $this->phase->duration_days; // 7 для первой фазы

        // Если тренировок меньше чем нужно, берем все
        if ($workouts->count() <= $workoutsNeeded) {
            return $workouts;
        }

        // Если у пользователя есть цель - подбираем под цель
        if ($this->userParameters && $this->userParameters->goal_id) {
            return $this->selectWorkoutsByGoal($workouts, $workoutsNeeded);
        }

        // Иначе просто случайные тренировки
        return $workouts->random($workoutsNeeded);
    }

    /**
     * Выбор тренировок по цели пользователя
     */
    protected function selectWorkoutsByGoal(Collection $workouts, int $needed): Collection
    {
        $goalId = $this->userParameters->goal_id;
        $preferredTypes = self::GOAL_WORKOUT_TYPES[$goalId] ?? [];

        if (empty($preferredTypes)) {
            return $workouts->random($needed);
        }

        // Разделяем тренировки на предпочтительные и остальные
        $preferred = $workouts->filter(function ($workout) use ($preferredTypes) {
            // Предполагаем, что у тренировок есть поле type
            // Если нет - можно определять по упражнениям или другим признакам
            return in_array($workout->type ?? 'general', $preferredTypes);
        });

        $other = $workouts->diff($preferred);

        // Сколько нужно взять из предпочтительных (минимум 70%)
        $preferredNeeded = min(
            ceil($needed * 0.7), // 70% тренировок под цель
            $preferred->count()
        );

        $otherNeeded = $needed - $preferredNeeded;

        $selected = collect();

        // Берем случайные из предпочтительных
        if ($preferredNeeded > 0 && $preferred->isNotEmpty()) {
            $selected = $selected->merge(
                $preferred->random(min($preferredNeeded, $preferred->count()))
            );
        }

        // Добираем из остальных
        if ($selected->count() < $needed && $other->isNotEmpty()) {
            $remaining = $needed - $selected->count();
            $selected = $selected->merge(
                $other->random(min($remaining, $other->count()))
            );
        }

        return $selected;
    }

    protected function adaptWorkouts(Collection $workouts): Collection
    {
        return $workouts->map(function ($workout) {
            $adaptedWorkout = clone $workout;
            $adaptedWorkout->exercises = $this->adaptExercises($workout->exercises);
            $adaptedWorkout->adaptation_notes = $this->getAdaptationNotes();

            return $adaptedWorkout;
        });
    }

    protected function adaptExercises(Collection $exercises): Collection
    {
        if (!$this->userParameters) {
            return $exercises;
        }

        return $exercises->map(function ($exercise) {
            $adaptedExercise = clone $exercise;

            if (!$this->isEquipmentAvailable($exercise)) {
                $alternative = $this->findAlternativeExercise($exercise);
                if ($alternative) {
                    return $alternative;
                }
            }

            // Адаптируем сеты и повторения
            $adaptedExercise->pivot->sets = $this->adjustSetsByLevel($exercise);
            $adaptedExercise->pivot->reps = $this->adjustRepsByLevel($exercise);

            // Добавляем вес пользователя
            $adaptedExercise->user_weight = $this->getUserExerciseWeight($exercise);

            return $adaptedExercise;
        });
    }

    protected function isEquipmentAvailable($exercise): bool
    {
        if (!$exercise->equipment_id) {
            return true;
        }
        return $this->userParameters->equipment_id === $exercise->equipment_id;
    }

    protected function findAlternativeExercise($originalExercise): ?object
    {
        $alternative = \App\Models\Exercise::where('muscle_group', $originalExercise->muscle_group)
            ->where('equipment_id', $this->userParameters->equipment_id)
            ->where('id', '!=', $originalExercise->id)
            ->inRandomOrder()
            ->first();

        if ($alternative) {
            $alternative->pivot = (object) [
                'sets' => $originalExercise->pivot->sets,
                'reps' => $originalExercise->pivot->reps,
                'order_number' => $originalExercise->pivot->order_number,
            ];
            $alternative->is_alternative = true;
            $alternative->original_exercise_id = $originalExercise->id;

            return $alternative;
        }
        return null;
    }

    protected function adjustSetsByLevel($exercise): int
    {
        $baseSets = $exercise->pivot->sets;
        $levelId = $this->userParameters->level_id;

        return match ($levelId) {
            1 => max(2, $baseSets - 1), // Начинающий
            2 => $baseSets,              // Средний
            3 => min(5, $baseSets + 1),  // Продвинутый
            default => $baseSets,
        };
    }

    protected function adjustRepsByLevel($exercise): string
    {
        $reps = $exercise->pivot->reps;
        $levelId = $this->userParameters->level_id;

        if (str_contains($reps, '-')) {
            [$min, $max] = explode('-', $reps);

            return match ($levelId) {
                1 => (intval($min) - 2) . '-' . (intval($max) - 2),
                3 => (intval($min) + 2) . '-' . (intval($max) + 2),
                default => $reps,
            };
        }
        return $reps;
    }

    protected function getAdaptationNotes(): array
    {
        $notes = [];
        if ($this->userParameters) {
            $notes[] = "Тренировка подобрана с учетом вашего уровня подготовки";

            if ($this->userParameters->goal) {
                $notes[] = "Акцент на достижение цели: {$this->userParameters->goal->name}";
            }
        }
        return $notes;
    }

    public function assignWorkoutsToUser(User $user, Collection $workouts): void
    {
        DB::transaction(function () use ($user, $workouts) {
            // Сначала удаляем старые незавершенные тренировки
            $user->userWorkouts()
                ->whereIn('status', ['pending', 'started'])
                ->delete();

            // Создаем новые
            foreach ($workouts as $workout) {
                UserWorkout::create([
                    'user_id' => $user->id,
                    'workout_id' => $workout->id,
                    'status' => 'started',
                    'started_at' => null,
                    'completed_at' => null,
                ]);
            }
        });
    }

    protected function getUserExerciseWeight(Exercise $exercise): ?float
    {
        $userWeight = \App\Models\UserExerciseWeight::where('user_id', $this->user->id)
            ->where('exercise_id', $exercise->id)
            ->first();

        return $userWeight?->weight;
    }
}
