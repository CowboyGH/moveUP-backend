<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Responses\ApiResponse;
use App\Models\UserWorkout;
use Illuminate\Http\Request;

class WarmupController extends BaseWorkoutController
{
    /**
     * Получить следующее упражнение разминки
     */
    public function nextWarmup(UserWorkout $userWorkout, Request $request)
    {
        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        $request->validate([
            'current_warmup_id' => 'nullable|exists:warmups,id',
        ]);

        $warmups = $this->getSortedWarmups($userWorkout);

        // Если это начало разминки, возвращаем первое упражнение
        if (!$request->current_warmup_id) {
            $firstWarmup = $warmups->first();

            if (!$firstWarmup) {
                // Если разминки нет, сразу переходим к первому упражнению
                return app(ExerciseController::class)->getFirstExercise($userWorkout);
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
        return app(ExerciseController::class)->getFirstExercise($userWorkout);
    }
}
