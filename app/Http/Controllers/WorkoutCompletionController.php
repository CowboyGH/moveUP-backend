<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workout\CompleteWorkoutWithReactionsRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Workout;
use App\Models\Exercise;
use App\Services\WorkoutLoadManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkoutCompletionController extends Controller
{
    protected WorkoutLoadManagerService $loadManager;

    public function __construct(WorkoutLoadManagerService $loadManager)
    {
        $this->loadManager = $loadManager;
    }

    public function completeWithAdjustments(CompleteWorkoutWithReactionsRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $workout = Workout::find($validated['workout_id']);
        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        $exerciseIds = array_column($validated['reactions'], 'exercise_id');
        $existingExercises = Exercise::whereIn('id', $exerciseIds)->pluck('id')->toArray();
        $missingExercises = array_diff($exerciseIds, $existingExercises);

        if (!empty($missingExercises)) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Упражнения не найдены: ' . implode(', ', $missingExercises),
                404
            );
        }

        $activeWorkout = $user->userWorkouts()
            ->where('workout_id', $validated['workout_id'])
            ->where('status', 'started')
            ->first();

        if (!$activeWorkout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная тренировка не найдена',
                404
            );
        }

        try {
            $result = $this->loadManager->completeWorkoutWithLoadAdjustment(
                $user,
                $validated['workout_id'],
                $validated['reactions']
            );

            return ApiResponse::success(
                'Тренировка успешно завершена. Нагрузка скорректирована для следующих тренировок.',
                $result
            );

        } catch (\Exception $e) {
            Log::error('Ошибка при завершении тренировки: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'workout_id' => $validated['workout_id'],
                'error' => $e->getTraceAsString()
            ]);

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обработке тренировки',
                500
            );
        }
    }
}
