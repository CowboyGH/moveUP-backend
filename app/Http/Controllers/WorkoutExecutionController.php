<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use App\Services\WorkoutLoadManagerService;
use App\Services\PhaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkoutExecutionController extends Controller
{
    protected ExerciseLoadService $exerciseLoadService;
    protected WorkoutLoadManagerService $loadManager;
    protected PhaseService $phaseService;

    public function __construct(
        ExerciseLoadService $exerciseLoadService,
        WorkoutLoadManagerService $loadManager,
        PhaseService $phaseService
    ) {
        $this->exerciseLoadService = $exerciseLoadService;
        $this->loadManager = $loadManager;
        $this->phaseService = $phaseService;
    }

    /**
     * Получить детали тренировки для выполнения
     */
    public function show(UserWorkout $userWorkout)
    {
        $user = request()->user();
        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Тренировка не принадлежит текущему пользователю',
                403
            );
        }
        $userWorkout->load([
            'workout.warmups',
            'workout.exercises' => function ($query) {
                $query->orderBy('pivot_order_number');
            }
        ]);
        $exercisesWithWeights = $userWorkout->workout->exercises->map(function ($exercise) use ($user) {
            $weight = $this->exerciseLoadService->getUserCurrentWeight($user->id, $exercise->id);

            return [
                'id' => $exercise->id,
                'title' => $exercise->title,
                'description' => $exercise->description,
                'image' => $exercise->image_url,
                'sets' => $exercise->pivot->sets,
                'reps' => $exercise->pivot->reps,
                'order_number' => $exercise->pivot->order_number,
                'current_weight' => $weight,
            ];
        })->sortBy('order_number')->values();

        $data = [
            'user_workout_id' => $userWorkout->id,
            'workout' => [
                'id' => $userWorkout->workout->id,
                'title' => $userWorkout->workout->title,
                'description' => $userWorkout->workout->description,
                'duration_minutes' => $userWorkout->workout->duration_minutes,
                'type' => $userWorkout->workout->type,
                'image' => $userWorkout->workout->image_url,
            ],
            'warmups' => $userWorkout->workout->warmups->map(function ($warmup) {
                return [
                    'id' => $warmup->id,
                    'name' => $warmup->name,
                    'description' => $warmup->description,
                    'image' => $warmup->image_url,
                    'duration_seconds' => 60,
                    'order_number' => $warmup->pivot->order_number,
                ];
            })->sortBy('order_number')->values(),
            'exercises' => $exercisesWithWeights,
            'started_at' => $userWorkout->started_at,
            'status' => $userWorkout->status,
        ];
        return ApiResponse::data($data, 'Детали тренировки');
    }

    /**
     * Получить следующее упражнение разминки
     */
    public function nextWarmup(UserWorkout $userWorkout, Request $request)
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(ErrorResponse::FORBIDDEN, 'Доступ запрещен', 403);
        }

        $request->validate([
            'current_warmup_id' => 'nullable|exists:warmups,id',
        ]);

        $workout = $userWorkout->workout()->with('warmups')->first();
        $warmups = $workout->warmups->sortBy('pivot.order_number');

        // Если это начало разминки, возвращаем первое упражнение
        if (!$request->current_warmup_id) {
            $firstWarmup = $warmups->first();

            if (!$firstWarmup) {
                // Если разминки нет, сразу переходим к первому упражнению
                return $this->getFirstExercise($userWorkout);
            }

            return ApiResponse::data([
                'type' => 'warmup',
                'warmup' => [
                    'id' => $firstWarmup->id,
                    'name' => $firstWarmup->name,
                    'description' => $firstWarmup->description,
                    'image' => $firstWarmup->image_url,
                    'duration_seconds' => 60,
                    'order_number' => $firstWarmup->pivot->order_number,
                    'is_last' => $warmups->count() === 1,
                ],
            ]);
        }

        // Ищем следующее упражнение разминки
        $currentWarmup = $warmups->firstWhere('id', $request->current_warmup_id);
        $currentIndex = $warmups->search(function ($item) use ($currentWarmup) {
            return $item->id === $currentWarmup->id;
        });

        $nextWarmup = $warmups->get($currentIndex + 1);

        if ($nextWarmup) {
            return ApiResponse::data([
                'type' => 'warmup',
                'warmup' => [
                    'id' => $nextWarmup->id,
                    'name' => $nextWarmup->name,
                    'description' => $nextWarmup->description,
                    'image' => $nextWarmup->image_url,
                    'duration_seconds' => 60,
                    'order_number' => $nextWarmup->pivot->order_number,
                    'is_last' => $currentIndex + 1 === $warmups->count() - 1,
                ],
            ]);
        }

        // Разминка закончена - переходим к первому упражнению
        return $this->getFirstExercise($userWorkout);
    }

    /**
     * Получить первое упражнение тренировки
     */
    private function getFirstExercise(UserWorkout $userWorkout)
    {
        $workout = $userWorkout->workout()->with('exercises')->first();
        $exercises = $workout->exercises->sortBy('pivot.order_number');

        $firstExercise = $exercises->first();

        if (!$firstExercise) {
            return ApiResponse::error(ErrorResponse::NOT_FOUND, 'В тренировке нет упражнений', 404);
        }

        $weight = $this->exerciseLoadService->getUserCurrentWeight($userWorkout->user_id, $firstExercise->id);

        return ApiResponse::data([
            'type' => 'exercise',
            'needs_weight_input' => $weight === null,
            'exercise' => [
                'id' => $firstExercise->id,
                'title' => $firstExercise->title,
                'description' => $firstExercise->description,
                'image' => $firstExercise->image_url,
                'sets' => $firstExercise->pivot->sets,
                'reps' => $firstExercise->pivot->reps,
                'order_number' => $firstExercise->pivot->order_number,
                'current_weight' => $weight,
                'is_last' => $exercises->count() === 1,
                'exercise_number' => 1,
                'total_exercises' => $exercises->count(),
            ],
        ]);
    }

    /**
     * Получить следующее упражнение
     */
    public function nextExercise(UserWorkout $userWorkout, Request $request)
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(ErrorResponse::FORBIDDEN, 'Доступ запрещен', 403);
        }

        $request->validate([
            'current_exercise_id' => 'required|exists:exercises,id',
            'weight_used' => 'nullable|numeric|min:0|max:500',
        ]);

        $workout = $userWorkout->workout()->with('exercises')->first();
        $exercises = $workout->exercises->sortBy('pivot.order_number');

        $currentExercise = $exercises->firstWhere('id', $request->current_exercise_id);
        $currentIndex = $exercises->search(function ($item) use ($currentExercise) {
            return $item->id === $currentExercise->id;
        });

        // Если пользователь ввел вес, сохраняем его
        if ($request->has('weight_used') && $request->weight_used) {
            $this->exerciseLoadService->saveExerciseWeight(
                $user->id,
                $request->current_exercise_id,
                $request->weight_used
            );
        }

        $nextExercise = $exercises->get($currentIndex + 1);

        if (!$nextExercise) {
            // Это было последнее упражнение - тренировка завершена
            return ApiResponse::data([
                'type' => 'completed',
                'message' => 'Все упражнения выполнены. Завершите тренировку.',
            ]);
        }

        $weight = $this->exerciseLoadService->getUserCurrentWeight($user->id, $nextExercise->id);

        return ApiResponse::data([
            'type' => 'exercise',
            'needs_weight_input' => $weight === null,
            'exercise' => [
                'id' => $nextExercise->id,
                'title' => $nextExercise->title,
                'description' => $nextExercise->description,
                'image' => $nextExercise->image_url,
                'sets' => $nextExercise->pivot->sets,
                'reps' => $nextExercise->pivot->reps,
                'order_number' => $nextExercise->pivot->order_number,
                'current_weight' => $weight,
                'is_last' => $currentIndex + 1 === $exercises->count() - 1,
                'exercise_number' => $currentIndex + 2,
                'total_exercises' => $exercises->count(),
            ],
        ]);
    }

    /**
     * Сохранить результат выполнения упражнения
     * Вызывается ПОСЛЕ выполнения каждого упражнения
     */
    public function saveExerciseResult(UserWorkout $userWorkout, Request $request)
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(ErrorResponse::FORBIDDEN, 'Доступ запрещен', 403);
        }

        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'reaction' => 'required|in:good,normal,bad',
            'weight_used' => 'nullable|numeric|min:0|max:500',
            'sets_completed' => 'nullable|integer|min:0|max:10',
            'reps_completed' => 'nullable|integer|min:0|max:50',
        ]);

        $performanceData = [
            'sets_completed' => $request->sets_completed,
            'reps_completed' => $request->reps_completed,
            'weight_used' => $request->weight_used,
        ];

        // Обрабатываем реакцию через ExerciseLoadService
        $result = $this->exerciseLoadService->processReaction(
            $user,
            $request->exercise_id,
            $request->reaction,
            $userWorkout->id,
            $performanceData
        );

        // Проверяем фазу отдыха и перегенерируем тренировки при необходимости
        if ($result['rest_phase'] && $result['rest_phase']['required']) {
            $this->checkAndRegenerateWorkouts($user, $request->exercise_id);
        }

        return ApiResponse::success('Результат упражнения сохранен', [
            'exercise_result' => $result,
            'next_url' => route('workout-execution.next-exercise', ['userWorkout' => $userWorkout->id]),
        ]);
    }

    /**
     * Завершить тренировку
     * Вызывается только после выполнения ВСЕХ упражнений
     * НЕ ПРИНИМАЕТ реакции - они уже сохранены через saveExerciseResult
     */
    public function complete(UserWorkout $userWorkout)
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(ErrorResponse::FORBIDDEN, 'Доступ запрещен', 403);
        }

        // Проверяем, что тренировка еще не завершена
        if ($userWorkout->status === 'completed') {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Тренировка уже завершена',
                409
            );
        }

        try {
            DB::beginTransaction();

            // Просто завершаем тренировку (реакции уже сохранены отдельно)
            $userWorkout->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Обновляем прогресс и проверяем переход на следующую фазу
            $this->phaseService->handleWorkoutCompletion($userWorkout);

            DB::commit();

            return ApiResponse::success('Тренировка успешно завершена!', [
                'user_workout' => [
                    'id' => $userWorkout->id,
                    'completed_at' => $userWorkout->completed_at,
                ],
                'phase_progress' => $user->currentProgress(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при завершении тренировки: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'workout_id' => $userWorkout->id,
                'error' => $e->getTraceAsString()
            ]);

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при завершении тренировки',
                500
            );
        }
    }

    /**
     * Проверить и перегенерировать тренировки при фазе отдыха
     */
    private function checkAndRegenerateWorkouts(User $user, int $exerciseId): void
    {
        $currentProgress = $user->currentProgress();

        if (!$currentProgress) {
            return;
        }

        // Проверяем, есть ли будущие тренировки с этим упражнением
        $activeWorkouts = $user->userWorkouts()
            ->with('workout.exercises')
            ->where('status', 'started')
            ->whereNull('started_at')
            ->get();

        $hasFutureExercises = false;
        foreach ($activeWorkouts as $userWorkout) {
            foreach ($userWorkout->workout->exercises as $exercise) {
                if ($exercise->id == $exerciseId) {
                    $hasFutureExercises = true;
                    break 2;
                }
            }
        }

        if ($hasFutureExercises) {
            Log::info("Фаза отдыха для упражнения {$exerciseId}. Перегенерация тренировок для пользователя {$user->id}");

            // Удаляем старые неначатые тренировки
            $user->userWorkouts()
                ->where('status', 'started')
                ->whereNull('started_at')
                ->delete();

            // Генерируем новые тренировки
            $workoutGenerator = app(\App\Services\WorkoutGeneration\WorkoutGeneratorService::class);
            $workouts = $workoutGenerator->generateForPhase($user, $currentProgress->phase);

            if ($workouts->isNotEmpty()) {
                $workoutGenerator->assignWorkoutsToUser($user, $workouts);
            }
        }
    }
}
