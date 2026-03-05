<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserProgressController extends Controller
{
    public function updateWeeklyGoal(Request $request): JsonResponse
    {
        $request->validate([
            'weekly_goal' => ['required', 'integer', 'min:1', 'max:7'],
        ]);

        $user = $request->user();
        $currentProgress = $user->currentProgress();

        if (!$currentProgress) {
            return response()->json([
                'success' => false,
                'message' => 'У пользователя нет активной фазы'
            ], 404);
        }

        $currentProgress->weekly_workout_goal = $request->weekly_goal;
        $currentProgress->save();

        return response()->json([
            'success' => true,
            'message' => 'Недельная цель обновлена',
            'data' => [
                'weekly_goal' => $currentProgress->weekly_workout_goal
            ]
        ]);
    }
}
