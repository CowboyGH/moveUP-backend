<?php

namespace App\Http\Controllers\Workouts\Execution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\NextWarmupRequest;
use App\Http\Requests\Workouts\SaveExerciseResultRequest;
use App\Models\UserWorkout;
use App\Services\Workouts\Execution\CompletionService;
use App\Services\Workouts\Execution\ExerciseService;
use App\Services\Workouts\Execution\ShowService;
use App\Services\Workouts\Execution\WarmupService;

class WorkoutExecutionController extends Controller
{
    public function __construct(
        private readonly ShowService $showService,
        private readonly WarmupService $warmupService,
        private readonly ExerciseService $exerciseService,
        private readonly CompletionService $completionService
    ) {}

    public function show(UserWorkout $userWorkout)
    {
        $this->authorize('view', $userWorkout);
        return $this->showService->show($userWorkout);
    }

    public function startWarmup(UserWorkout $userWorkout)
    {
        $this->authorize('view', $userWorkout);
        return $this->warmupService->startWarmup($userWorkout);
    }

    public function completeWarmup(UserWorkout $userWorkout)
    {
        $this->authorize('view', $userWorkout);
        return $this->warmupService->completeWarmup($userWorkout);
    }

    public function nextWarmup(UserWorkout $userWorkout, NextWarmupRequest $request)
    {
        $this->authorize('view', $userWorkout);
        return $this->warmupService->nextWarmup($userWorkout, $request);
    }

    public function saveExerciseResult(UserWorkout $userWorkout, SaveExerciseResultRequest $request)
    {
        $this->authorize('view', $userWorkout);
        return $this->exerciseService->saveExerciseResult($userWorkout, $request);
    }

    public function complete(UserWorkout $userWorkout)
    {
        $this->authorize('view', $userWorkout);
        return $this->completionService->complete($userWorkout);
    }
}
