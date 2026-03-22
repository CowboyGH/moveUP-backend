<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workout\NextExerciseRequest;
use App\Http\Requests\Workout\NextWarmupRequest;
use App\Http\Requests\Workout\SaveExerciseResultRequest;
use App\Models\UserWorkout;
use Illuminate\Http\Request;

class WorkoutExecutionController extends Controller
{
    protected ShowController $showController;
    protected WarmupController $warmupController;
    protected ExerciseController $exerciseController;
    protected CompletionController $completionController;

    public function __construct(
        ShowController $showController,
        WarmupController $warmupController,
        ExerciseController $exerciseController,
        CompletionController $completionController
    ) {
        $this->showController = $showController;
        $this->warmupController = $warmupController;
        $this->exerciseController = $exerciseController;
        $this->completionController = $completionController;
    }

    public function show(UserWorkout $userWorkout)
    {
        return $this->showController->show($userWorkout);
    }
    public function startWarmup(UserWorkout $userWorkout)
    {
        return $this->warmupController->startWarmup($userWorkout);
    }
    public function completeWarmup(UserWorkout $userWorkout)
    {
        return $this->warmupController->completeWarmup($userWorkout);
    }
    public function nextWarmup(UserWorkout $userWorkout, NextWarmupRequest $request)
    {
        return $this->warmupController->nextWarmup($userWorkout, $request);
    }

    public function nextExercise(UserWorkout $userWorkout, NextExerciseRequest $request)
    {
        return $this->exerciseController->nextExercise($userWorkout, $request);
    }

    public function saveExerciseResult(UserWorkout $userWorkout, SaveExerciseResultRequest $request)
    {
        return $this->exerciseController->saveExerciseResult($userWorkout, $request);
    }

    public function complete(UserWorkout $userWorkout)
    {
        return $this->completionController->complete($userWorkout);
    }
}
