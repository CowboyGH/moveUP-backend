<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Requests\Workout\NextWarmupRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use Illuminate\Support\Facades\Auth;

class WarmupController extends BaseWorkoutController
{
    /**
     * Начинаем разминку
     */
    public function startWarmup(UserWorkout $userWorkout)
    {
        if (!Auth::check()) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }
        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }
        $warmups = $this->getSortedWarmups($userWorkout);

        if ($warmups->isEmpty()) {
            return $this->startWorkout($userWorkout);
        }
        $userWorkout->update([
            'status' => UserWorkout::STATUS_STARTED,
            'started_at' => now(),
        ]);

        $firstWarmup = $warmups->first();

        return ApiResponse::data([
            'type' => 'warmup',
            'user_workout_id' => $userWorkout->id,
            'warmup' => [
                'id' => $firstWarmup->id,
                'name' => $firstWarmup->name,
                'description' => $firstWarmup->description,
                'image' => $firstWarmup->image_url,
                'duration_seconds' => 60,
                'order_number' => $firstWarmup->pivot->order_number,
                'is_last' => $warmups->count() === 1,
            ],
            'total_warmups' => $warmups->count(),
        ], 'Разминка начата');
    }

    protected function startWorkout(UserWorkout $userWorkout)
    {
        $userWorkout->update([
            'status' => UserWorkout::STATUS_STARTED,
            'started_at' => now(),
        ]);
        return app(ExerciseController::class)->getFirstExercise($userWorkout);
    }

    /**
     * Получить следующее упражнение разминки
     */
    public function nextWarmup(UserWorkout $userWorkout, NextWarmupRequest $request)
    {
        if (!Auth::check()) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }
        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        $warmups = $this->getSortedWarmups($userWorkout);

        if (!$request->current_warmup_id) {
            $firstWarmup = $warmups->first();

            if (!$firstWarmup) {
                return $this->startWorkout($userWorkout);
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
        return $this->startWorkout($userWorkout);
    }

    /**
     * Завершение разминки
     */
    public function completeWarmup(UserWorkout $userWorkout)
    {
        if (!Auth::check()) {
            return ApiResponse::error(
                ErrorResponse::UNAUTHORIZED,
                'Пользователь не авторизован',
                401
            );
        }
        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        if ($userWorkout->status !== UserWorkout::STATUS_STARTED) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Тренировка не в статусе "начата"',
                409
            );
        }
        return $this->startWorkout($userWorkout);
    }
}
