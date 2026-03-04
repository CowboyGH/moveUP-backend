<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\User;
use App\Services\WorkoutGeneratorService;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutGeneratorController extends Controller
{
    protected WorkoutGeneratorService $workoutGenerator;
    protected PhaseService $phaseService;

    public function __construct(
        WorkoutGeneratorService $workoutGenerator,
        PhaseService $phaseService
    ) {
        $this->workoutGenerator = $workoutGenerator;
        $this->phaseService = $phaseService;
    }

    public function generateForUser(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }

        $currentProgress = $user->currentProgress();
        if (!$currentProgress) {
            return ApiResponse::error('User has no active phase', 400);
        }

        $workouts = $this->workoutGenerator->generateForPhase(
            $user,
            $currentProgress->phase
        );

        if ($workouts->isEmpty()) {
            return ApiResponse::error('No workouts generated', 404);
        }

        $this->workoutGenerator->assignWorkoutsToUser($user, $workouts);

        return ApiResponse::data([
            'message' => 'Workouts generated successfully',
            'count' => $workouts->count()
        ]);
    }

    public function regenerateForUser(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }
        $user->userWorkouts()
            ->where('status', 'started')
            ->delete();

        return $this->generateForUser($userId);
    }
}
