<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exercise\ReactToExerciseRequest;
use App\Http\Requests\Exercise\GetLoadRecommendationRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ExerciseReactionController extends Controller
{
    protected ExerciseLoadService $exerciseLoadService;
    protected WorkoutGeneratorService $workoutGenerator;
    protected PhaseService $phaseService;

    public function __construct(
        ExerciseLoadService $exerciseLoadService,
        WorkoutGeneratorService $workoutGenerator,
        PhaseService $phaseService
    ) {
        $this->exerciseLoadService = $exerciseLoadService;
        $this->workoutGenerator = $workoutGenerator;
        $this->phaseService = $phaseService;
    }

    /**
     * Оценка выполненного упражнения
     */
    public function react(ReactToExerciseRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Проверяем существование упражнения
        $exercise = \App\Models\Exercise::find($validated['exercise_id']);
        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        // Проверяем существование тренировки
        $userWorkout = UserWorkout::find($validated['user_workout_id']);
        if (!$userWorkout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        // Проверяем, что тренировка принадлежит пользователю
        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Тренировка не принадлежит текущему пользователю',
                403
            );
        }

        // Проверяем, не отправлялась ли уже реакция для этого упражнения в рамках этой тренировки
        $existingReaction = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->where('exercise_id', $validated['exercise_id'])
            ->where('user_workout_id', $validated['user_workout_id'])
            ->first();

        if ($existingReaction) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Реакция на это упражнение уже была отправлена',
                409
            );
        }

        // Подготавливаем данные о производительности
        $performanceData = [
            'sets_completed' => $validated['sets_completed'] ?? null,
            'reps_completed' => $validated['reps_completed'] ?? null,
            'weight_used' => $validated['weight_used'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        // Обрабатываем оценку
        $result = $this->exerciseLoadService->processReaction(
            $user,
            $validated['exercise_id'],
            $validated['reaction'],
            $validated['user_workout_id'],
            $performanceData
        );

        // Проверяем, не наступила ли фаза отдыха для упражнения
        if ($result['rest_phase'] && $result['rest_phase']['required']) {
            $this->checkAndRegenerateWorkouts($user, $validated['exercise_id'], $result['rest_phase']['duration_days']);
        }

        return ApiResponse::success('Оценка упражнения сохранена', $result);
    }

    private function checkAndRegenerateWorkouts(User $user, int $exerciseId, int $restDays): void
    {
        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            return;
        }

        // Получаем все активные тренировки пользователя
        $activeWorkouts = $user->userWorkouts()
            ->with('workout.exercises')
            ->where('status', 'started')
            ->get();

        // Проверяем, есть ли среди будущих тренировок (не начатых) это упражнение
        $hasFutureExercises = false;
        foreach ($activeWorkouts as $userWorkout) {
            if ($userWorkout->started_at !== null) {
                continue;
            }

            // Проверяем, есть ли проблемное упражнение в тренировке
            foreach ($userWorkout->workout->exercises as $exercise) {
                if ($exercise->id == $exerciseId) {
                    $hasFutureExercises = true;
                    break 2;
                }
            }
        }

        if ($hasFutureExercises) {
            Log::info("Фаза отдыха для упражнения {$exerciseId} на {$restDays} дней. Перегенерация тренировок для пользователя {$user->id}");

            $user->userWorkouts()
                ->where('status', 'started')
                ->whereNull('started_at')
                ->delete();

            $workouts = $this->workoutGenerator->generateForPhase($user, $currentProgress->phase);
            if ($workouts->isNotEmpty()) {
                $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);
                Log::info("Сгенерировано {$workouts->count()} тренировок для пользователя {$user->id} после фазы отдыха");
            }
        }
    }

    /**
     * Получить историю оценок для упражнения
     */
    public function history(int $exerciseId): JsonResponse
    {
        $user = request()->user();

        // Проверяем существование упражнения
        $exercise = \App\Models\Exercise::find($exerciseId);
        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        $history = $this->exerciseLoadService->getReactionHistory(
            $user->id,
            $exerciseId,
            30 // последние 30 дней
        );

        $analysis = $this->exerciseLoadService->analyzeReactionPattern($history);

        return ApiResponse::data([
            'history' => $history,
            'analysis' => $analysis,
        ]);
    }

    /**
     * Получить рекомендацию по нагрузке для упражнения
     */
    public function recommendation(GetLoadRecommendationRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Проверяем существование упражнения
        $exercise = \App\Models\Exercise::find($validated['exercise_id']);
        if (!$exercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнение не найдено',
                404
            );
        }

        $recommendation = $this->exerciseLoadService->getLoadRecommendation(
            $user,
            $validated['exercise_id']
        );

        return ApiResponse::data($recommendation);
    }

    /**
     * Получить статистику по всем упражнениям пользователя
     */
    public function statistics(): JsonResponse
    {
        $user = request()->user();

        // Получаем все оценки пользователя
        $reactions = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->with('exercise')
            ->orderBy('reaction_date', 'desc')
            ->get()
            ->groupBy('exercise_id');

        $statistics = [];
        foreach ($reactions as $exerciseId => $exerciseReactions) {
            $analysis = $this->exerciseLoadService->analyzeReactionPattern($exerciseReactions);
            $exercise = $exerciseReactions->first()->exercise;

            $statistics[] = [
                'exercise_id' => $exerciseId,
                'exercise_name' => $exercise ? $exercise->title : 'Упражнение удалено',
                'total_reactions' => $exerciseReactions->count(),
                'last_reaction' => $exerciseReactions->first()->reaction,
                'last_reaction_date' => $exerciseReactions->first()->reaction_date->format('Y-m-d'),
                'analysis' => $analysis,
            ];
        }

        // Общая статистика
        $totalReactions = \App\Models\ExerciseReaction::where('user_id', $user->id)->count();
        $goodCount = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->where('reaction', 'good')
            ->count();
        $badCount = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->where('reaction', 'bad')
            ->count();
        $normalCount = $totalReactions - $goodCount - $badCount;

        return ApiResponse::data([
            'summary' => [
                'total_reactions' => $totalReactions,
                'good_percentage' => $totalReactions > 0 ? round(($goodCount / $totalReactions) * 100) : 0,
                'normal_percentage' => $totalReactions > 0 ? round(($normalCount / $totalReactions) * 100) : 0,
                'bad_percentage' => $totalReactions > 0 ? round(($badCount / $totalReactions) * 100) : 0,
                'exercises_with_reactions' => $reactions->count(),
            ],
            'exercises' => $statistics,
        ]);
    }
}
