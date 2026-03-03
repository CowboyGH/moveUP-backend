<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Models\Workout;
use App\Models\UserWorkout;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ExerciseLoadService;
use App\Http\Requests\Workout\CompleteWorkoutWithReactionsRequest;

class WorkoutController extends Controller
{
    public function index(): JsonResponse
    {
        $workouts = Workout::where('is_active', 1)
            ->with(['phase', 'exercises', 'warmups'])
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'duration_minutes' => $workout->duration_minutes,
                    'phase' => $workout->phase ? [
                        'id' => $workout->phase->id,
                        'name' => $workout->phase->name,
                    ] : null,
                    'exercises_count' => $workout->exercises->count(),
                    'warmups_count' => $workout->warmups->count(),
                ];
            });

        return ApiResponse::data($workouts);
    }

    public function show(int $id): JsonResponse
    {
        $workout = Workout::where('id', $id)
            ->where('is_active', 1)
            ->with(['phase', 'exercises', 'warmups'])
            ->first();

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        $formattedExercises = $workout->exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'sets' => $exercise->pivot->sets,
                'reps' => $exercise->pivot->reps,
                'order_number' => $exercise->pivot->order_number,
            ];
        })->sortBy('order_number')->values();

        $formattedWarmups = $workout->warmups->map(function ($warmup) {
            return [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'order_number' => $warmup->pivot->order_number,
            ];
        })->sortBy('order_number')->values();

        $data = [
            'id' => $workout->id,
            'title' => $workout->title,
            'description' => $workout->description,
            'duration_minutes' => $workout->duration_minutes,
            'phase' => $workout->phase ? [
                'id' => $workout->phase->id,
                'name' => $workout->phase->name,
            ] : null,
            'exercises' => $formattedExercises,
            'warmups' => $formattedWarmups,
        ];

        return ApiResponse::data($data);
    }

    public function myWorkoutHistory(): JsonResponse
    {
        $user = auth()->user();

        $userWorkouts = UserWorkout::with(['workout', 'exercisePerformances', 'userWarmupPerformances.warmup'])
            ->where('user_id', $user->id)
            ->orderBy('started_at', 'desc')->get();

        $activeWorkout = $userWorkouts->where('status', 'started')->first();

        $formattedHistory = $userWorkouts->map(function ($userWorkout) {
            $workout = $userWorkout->workout;

            $totalExercises = $userWorkout->exercisePerformances->count();
            $completedExercises = $userWorkout->exercisePerformances->where('completed', true)->count();

            $totalWarmups = $userWorkout->userWarmupPerformances->count();
            $completedWarmups = $userWorkout->userWarmupPerformances->where('completed', true)->count();

            return [
                'id' => $userWorkout->id,
                'workout' => [
                    'id' => $workout ? $workout->id : null,
                    'title' => $workout ? $workout->title : 'Тренировка удалена',
                ],
                'started_at' => $userWorkout->started_at ? $userWorkout->started_at->format('Y-m-d H:i:s') : null,
                'completed_at' => $userWorkout->completed_at ? $userWorkout->completed_at->format('Y-m-d H:i:s') : null,
                'status' => $userWorkout->status,
                'duration' => $userWorkout->completed_at && $userWorkout->started_at
                    ? (int) $userWorkout->started_at->diffInMinutes($userWorkout->completed_at)
                    : null,
                'progress' => [
                    'exercises_completed' => $completedExercises,
                    'exercises_total' => $totalExercises,
                    'warmups_completed' => $completedWarmups,
                    'warmups_total' => $totalWarmups,
                ],
            ];
        });

        $statistics = [
            'total_workouts_started' => $userWorkouts->count(),
            'total_workouts_completed' => $userWorkouts->where('status', 'completed')->count(),
            'total_workouts_in_progress' => $userWorkouts->where('status', 'in_progress')->count(),
            'last_workout_date' => $userWorkouts->isNotEmpty() && $userWorkouts->first()->completed_at
                ? $userWorkouts->first()->completed_at->format('Y-m-d H:i:s')
                : null,
        ];

        $data = [
            'active' => $activeWorkout ? [
                'id' => $activeWorkout->id,
                'workout_id' => $activeWorkout->workout_id,
                'title' => $activeWorkout->workout ? $activeWorkout->workout->title : 'Тренировка удалена',
                'started_at' => $activeWorkout->started_at->format('Y-m-d H:i:s'),
                'duration_minutes' => (int) $activeWorkout->started_at->diffInMinutes(now()),
            ] : null,
            'statistics' => $statistics,
            'history' => $formattedHistory,
        ];

        return ApiResponse::data($data);
    }

    private function getWorkoutStatus(UserWorkout $userWorkout): string
    {
        return $userWorkout->status;
    }

    public function completeWorkout(Request $request, $workoutId, PhaseService $phaseService)
    {
        $userWorkout = UserWorkout::where('user_id', $request->user()->id)
            ->where('workout_id', $workoutId)
            ->where('status', 'started')
            ->first();

        if ($userWorkout) {
            $userWorkout->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            $phaseService->handleWorkoutCompletion($userWorkout);
            $this->checkAndAssignNextPhase($request->user(), $phaseService);

            return response()->json([
                'success' => true,
                'message' => 'Workout completed successfully'
            ]);
        }
    }
    protected function checkAndAssignNextPhase(User $user, PhaseService $phaseService)
    {
        $currentProgress = $user->currentProgress();

        if ($currentProgress && $currentProgress->canAdvanceToNextPhase()) {
            $nextPhase = $currentProgress->phase->nextPhase();

            if ($nextPhase) {
                $phaseService->assignPhaseToUser($user, $nextPhase);
                $phaseService->assignWorkoutsForNewPhase($user, $nextPhase);
            }
        }
    }
    public function completeWorkoutWithReactions(
        CompleteWorkoutWithReactionsRequest $request,
        PhaseService $phaseService,
        ExerciseLoadService $exerciseLoadService
    ) {
        $userWorkout = UserWorkout::where('user_id', $request->user()->id)
            ->where('workout_id', $request->workout_id)
            ->where('status', 'started')
            ->first();

        if (!$userWorkout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная тренировка не найдена',
                404
            );
        }

        // Обрабатываем все оценки упражнений
        $reactionsResults = [];
        foreach ($request->reactions as $reaction) {
            $result = $exerciseLoadService->processReaction(
                $request->user(),
                $reaction['exercise_id'],
                $reaction['reaction'],
                $userWorkout->id,
                $reaction['performance'] ?? null
            );
            $reactionsResults[] = $result;
        }

        // Завершаем тренировку
        $userWorkout->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Обрабатываем завершение тренировки в PhaseService
        $phaseService->handleWorkoutCompletion($userWorkout);

        // Проверяем и назначаем следующую фазу
        $this->checkAndAssignNextPhase($request->user(), $phaseService);

        // Анализируем общий прогресс на основе всех оценок
        $overallAnalysis = $this->analyzeOverallProgress($reactionsResults);

        return ApiResponse::success('Тренировка успешно завершена', [
            'workout_id' => $userWorkout->id,
            'completed_at' => $userWorkout->completed_at,
            'reactions_processed' => count($reactionsResults),
            'overall_analysis' => $overallAnalysis,
            'phase_progress' => $request->user()->currentProgress(),
        ]);
    }

    private function analyzeOverallProgress(array $reactionsResults): array
    {
        $goodCount = 0;
        $badCount = 0;
        $adjustmentsCount = 0;
        $restPhasesCount = 0;

        foreach ($reactionsResults as $result) {
            if ($result['reaction']->reaction === 'good') $goodCount++;
            if ($result['reaction']->reaction === 'bad') $badCount++;
            if ($result['adjustments']['applied']) $adjustmentsCount++;
            if ($result['rest_phase']) $restPhasesCount++;
        }

        $total = count($reactionsResults);

        return [
            'summary' => [
                'good_percentage' => $total > 0 ? round(($goodCount / $total) * 100) : 0,
                'bad_percentage' => $total > 0 ? round(($badCount / $total) * 100) : 0,
                'adjustments_applied' => $adjustmentsCount,
                'rest_phases_recommended' => $restPhasesCount,
            ],
            'message' => $this->getOverallMessage($goodCount, $badCount, $total),
        ];
    }

    private function getOverallMessage(int $good, int $bad, int $total): string
    {
        if ($total === 0) return 'Нет данных для анализа';

        $goodRatio = $good / $total;

        if ($goodRatio >= 0.8) {
            return 'Отличная тренировка! Прогресс налицо.';
        } elseif ($goodRatio >= 0.6) {
            return 'Хорошая тренировка. Продолжайте в том же духе.';
        } elseif ($goodRatio >= 0.4) {
            return 'Неплохая тренировка. Есть куда расти.';
        } else {
            return 'Тренировка была тяжелой. Не отчаивайтесь, завтра будет лучше!';
        }
    }
}
