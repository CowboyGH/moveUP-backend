<?php

namespace App\Services;

use App\Models\User;
use App\Models\Phase;
use App\Models\UserProgress;
use App\Models\UserWorkout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhaseService
{
    /**
     * Назначить начальную фазу для нового пользователя
     */
    public function assignInitialPhase(User $user): UserProgress
    {
        $firstPhase = Phase::getFirstPhase();

        if (!$firstPhase) {
            throw new \Exception('В системе не обнаружено фаз');
        }
        return UserProgress::create([
            'user_id' => $user->id,
            'phase_id' => $firstPhase->id,
            'streak_days' => 0,
            'completed_workouts' => 0,
        ]);
    }

    /**
     * Проверить и обновить фазу пользователя после тренировки
     */
    public function checkAndAdvancePhase(User $user): ?Phase
    {
        $currentProgress = $user->currentProgress();

        if (!$currentProgress) {
            $currentProgress = $this->assignInitialPhase($user);
        }
        if ($currentProgress->canAdvanceToNextPhase()) {
            return $this->advanceToNextPhase($user, $currentProgress);
        }

        return $currentProgress->phase;
    }

    /**
     * Перевести пользователя на следующую фазу
     */
    protected function advanceToNextPhase(User $user, UserProgress $currentProgress): Phase
    {
        $currentPhase = $currentProgress->phase;
        $nextPhase = $currentPhase->nextPhase();

        if (!$nextPhase) {
            $nextPhase = Phase::getFirstPhase();
        }

        $newProgress = UserProgress::create([
            'user_id' => $user->id,
            'phase_id' => $nextPhase->id,
            'streak_days' => 0,
            'completed_workouts' => 0,
        ]);

        Log::info("Пользователь {$user->id} перешел с фазы {$currentPhase->id} на фазу {$nextPhase->id}");

        return $nextPhase;
    }

    /**
     * Обновить прогресс пользователя после завершения тренировки
     */
    public function handleWorkoutCompletion(UserWorkout $userWorkout): void
    {
        DB::transaction(function () use ($userWorkout) {
            $user = $userWorkout->user;
            $currentProgress = $user->currentProgress();

            if (!$currentProgress) {
                $currentProgress = $this->assignInitialPhase($user);
            }
            $currentProgress->updateStreakAfterWorkout();
            $this->checkAndAdvancePhase($user);
        });
    }

    /**
     * Получить детальную информацию о прогрессе пользователя
     */
    public function getUserPhaseProgress(User $user): array
    {
        $currentProgress = $user->currentProgress();

        if (!$currentProgress) {
            return [
                'has_progress' => false,
                'message' => 'Пользователю не назначена фаза'
            ];
        }
        $currentPhase = $currentProgress->phase;
        $phaseStart = $currentProgress->created_at->startOfDay();
        $today = now()->startOfDay();

        $daysPassed = $phaseStart->diffInDays($today);
        $daysLeft = max(0, $currentPhase->duration_days - $daysPassed);
        $nextPhase = $currentPhase->nextPhase() ?? Phase::getFirstPhase();

        $recentWorkouts = $user->userWorkouts()
            ->with('workout')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->limit(5)
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'workout_name' => $workout->workout->title ?? 'Unknown',
                    'completed_at' => $workout->completed_at,
                    'duration' => $workout->started_at && $workout->completed_at
                        ? $workout->started_at->diffInMinutes($workout->completed_at)
                        : null,
                ];
            });
        return [
            'has_progress' => true,
            'current_phase' => [
                'id' => $currentPhase->id,
                'name' => $currentPhase->name,
                'description' => $currentPhase->description,
                'duration_days' => $currentPhase->duration_days,
                'min_workouts' => $currentPhase->min_workouts,
                'order_number' => $currentPhase->order_number,
            ],
            'progress' => [
                'streak_days' => $currentProgress->streak_days,
                'completed_workouts' => $currentProgress->completed_workouts,
                'days_passed' => $daysPassed,
                'days_left' => max(0, $currentPhase->duration_days - $daysPassed),
                'workouts_left' => max(0, $currentPhase->min_workouts - $currentProgress->completed_workouts),
                'phase_started_at' => $currentProgress->created_at,
                'last_workout_date' => $currentProgress->getLastWorkoutDateAttribute(),
                'has_workout_today' => $currentProgress->hasWorkoutToday(),
            ],
            'next_phase' => $nextPhase ? [
                'id' => $nextPhase->id,
                'name' => $nextPhase->name,
                'order_number' => $nextPhase->order_number,
            ] : null,
            'recent_workouts' => $recentWorkouts,
            'can_advance' => $currentProgress->canAdvanceToNextPhase(),
        ];
    }
}
