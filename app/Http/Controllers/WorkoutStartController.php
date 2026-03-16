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
        $existingStarted = UserWorkout::where('user_id', $user->id)
            ->where('status', UserWorkout::STATUS_STARTED)
            ->first();

        if ($existingStarted) {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'У вас уже есть активная тренировка',
                409
            );
        }
        $userWorkout = UserWorkout::where('user_id', $user->id)
            ->where('workout_id', $request->workout_id)
            ->where('status', UserWorkout::STATUS_ASSIGNED)
            ->first();

        if (!$userWorkout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена или уже начата',
                404
            );
        }
        $userWorkout->update([
            'status' => UserWorkout::STATUS_STARTED,
            'started_at' => now(),
        ]);
        return ApiResponse::success('Тренировка начата', [
            'user_workout_id' => $userWorkout->id,
            'started_at' => $userWorkout->started_at
        ]);
    }
}
