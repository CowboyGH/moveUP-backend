<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use App\Services\WorkoutLoadManagerService;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class BaseWorkoutController extends Controller
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
    protected function checkOwnership(UserWorkout $userWorkout): ?JsonResponse
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Тренировка не принадлежит текущему пользователю',
                403
            );
        }

        return null;
    }
    protected function getSortedExercises(UserWorkout $userWorkout): Collection
    {
        $workout = $userWorkout->workout()->with('exercises')->first();
        return $workout->exercises->sortBy('pivot.order_number');
    }
    protected function getSortedWarmups(UserWorkout $userWorkout): Collection
    {
        $workout = $userWorkout->workout()->with('warmups')->first();
        return $workout->warmups->sortBy('pivot.order_number');
    }

    public function getExerciseLoadService(): ExerciseLoadService
    {
        return $this->exerciseLoadService;
    }
}
