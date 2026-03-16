<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Controllers\Controller;
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

    public function nextWarmup(UserWorkout $userWorkout, Request $request)
    {
        return $this->warmupController->nextWarmup($userWorkout, $request);
    }

    public function nextExercise(UserWorkout $userWorkout, Request $request)
    {
        return $this->exerciseController->nextExercise($userWorkout, $request);
    }

    public function saveExerciseResult(UserWorkout $userWorkout, Request $request)
    {
        return $this->exerciseController->saveExerciseResult($userWorkout, $request);
    }

    public function complete(UserWorkout $userWorkout)
    {
        return $this->completionController->complete($userWorkout);
    }
}
