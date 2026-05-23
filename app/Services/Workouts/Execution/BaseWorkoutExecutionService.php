<?php

namespace App\Services\Workouts\Execution;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

abstract class BaseWorkoutExecutionService
{
    public function __construct(
        protected readonly ExerciseLoadService $exerciseLoadService,
        protected readonly PhaseService $phaseService
    ) {}

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

        return $workout->exercises->sortBy('pivot.order_number')->values();
    }

    protected function getSortedWarmups(UserWorkout $userWorkout): Collection
    {
        $workout = $userWorkout->workout()->with('warmups')->first();

        return $workout->warmups->sortBy('pivot.order_number')->values();
    }
}
