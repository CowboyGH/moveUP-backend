<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use Illuminate\Http\Request;

class WorkoutStartController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'workout_id' => 'required|exists:workouts,id'
        ]);

        $user = $request->user();

        $existingWorkout = UserWorkout::where('user_id', $user->id)
            ->where('status', 'started')
            ->first();

        if ($existingWorkout) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'У вас уже есть активная тренировка',
                409
            );
        }

        $userWorkout = UserWorkout::create([
            'user_id' => $user->id,
            'workout_id' => $request->workout_id,
            'started_at' => now(),
            'status' => 'started'
        ]);

        return ApiResponse::success('Тренировка начата', [
            'user_workout_id' => $userWorkout->id,
            'started_at' => $userWorkout->started_at
        ]);
    }
}
