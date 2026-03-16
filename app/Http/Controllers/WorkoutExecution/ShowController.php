<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Responses\ApiResponse;
use App\Models\UserWorkout;

class ShowController extends BaseWorkoutController
{
    public function show(UserWorkout $userWorkout)
    {
        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        $userWorkout->load([
            'workout.warmups',
            'workout.exercises' => function ($query) {
                $query->orderBy('pivot_order_number');
            }
        ]);

        $exercisesWithWeights = $userWorkout->workout->exercises->map(function ($exercise) use ($userWorkout) {
            $weight = $this->exerciseLoadService->getUserCurrentWeight($userWorkout->user_id, $exercise->id);

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
}
