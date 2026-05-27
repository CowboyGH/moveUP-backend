<?php

namespace App\Http\Controllers\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserWorkout;
use Illuminate\Http\JsonResponse;

class WorkoutController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $userWorkouts = UserWorkout::with(['workout.phase', 'workout.exercises', 'workout.warmups'])
            ->where('user_id', $user->id)
            ->whereIn('status', [UserWorkout::STATUS_ASSIGNED, UserWorkout::STATUS_STARTED])
            ->get();

        $formattedWorkouts = $userWorkouts->map(function ($userWorkout) {
            $workout = $userWorkout->workout;

            return [
                'user_workout_id' => $userWorkout->id,
                'workout' => [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'duration_minutes' => $workout->duration_minutes,
                    'type' => $workout->type,
                    'image' => $workout->image_url,
                ],
                'phase' => $workout->phase ? [
                    'id' => $workout->phase->id,
                    'name' => $workout->phase->name,
                ] : null,
                'exercises_count' => $workout->exercises->count(),
                'warmups_count' => $workout->warmups->count(),
                'status' => $userWorkout->status,
                'can_be_started' => $userWorkout->canBeStarted(),
                'is_started' => $userWorkout->isStarted(),
                'started_at' => $userWorkout->started_at,
            ];
        });
        $assigned = $formattedWorkouts->where('status', UserWorkout::STATUS_ASSIGNED)->values();
        $started = $formattedWorkouts->where('status', UserWorkout::STATUS_STARTED)->values();

        return ApiResponse::data([
            'assigned' => $assigned,
            'started' => $started,
            'has_active' => $started->isNotEmpty(),
        ]);
    }

}
