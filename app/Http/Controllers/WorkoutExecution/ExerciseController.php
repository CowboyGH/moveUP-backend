<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use Illuminate\Http\Request;

class ExerciseController extends BaseWorkoutController
{
    public function getFirstExercise(UserWorkout $userWorkout)
    {
        $exercises = $this->getSortedExercises($userWorkout);
        $firstExercise = $exercises->first();

        if (!$firstExercise) {
            return ApiResponse::error(ErrorResponse::NOT_FOUND, 'В тренировке нет упражнений', 404);
        }

        $weight = $this->exerciseLoadService->getUserCurrentWeight($userWorkout->user_id, $firstExercise->id);

        return ApiResponse::data([
            'type' => 'exercise',
            'needs_weight_input' => $weight === null,
            'exercise' => [
                'id' => $firstExercise->id,
                'title' => $firstExercise->title,
                'description' => $firstExercise->description,
                'image' => $firstExercise->image_url,
                'sets' => $firstExercise->pivot->sets,
                'reps' => $firstExercise->pivot->reps,
                'order_number' => $firstExercise->pivot->order_number,
                'current_weight' => $weight,
                'is_last' => $exercises->count() === 1,
                'exercise_number' => 1,
                'total_exercises' => $exercises->count(),
            ],
        ]);
    }

    public function nextExercise(UserWorkout $userWorkout, Request $request)
    {
        $user = request()->user();

        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        $request->validate([
            'current_exercise_id' => 'required|exists:exercises,id',
            'weight_used' => 'nullable|numeric|min:0|max:500',
        ]);

        $exercises = $this->getSortedExercises($userWorkout);

        $currentExercise = $exercises->firstWhere('id', $request->current_exercise_id);
        $currentIndex = $exercises->search(function ($item) use ($currentExercise) {
            return $item->id === $currentExercise->id;
        });

        // Если пользователь ввел вес, сохраняем его
        if ($request->has('weight_used') && $request->weight_used) {
            $this->exerciseLoadService->saveExerciseWeight(
                $user->id,
                $request->current_exercise_id,
                $request->weight_used
            );
        }

        $nextExercise = $exercises->get($currentIndex + 1);

        if (!$nextExercise) {
            // Это было последнее упражнение - тренировка завершена
            return ApiResponse::data([
                'type' => 'completed',
                'message' => 'Все упражнения выполнены. Завершите тренировку.',
            ]);
        }

        $weight = $this->exerciseLoadService->getUserCurrentWeight($user->id, $nextExercise->id);

        return ApiResponse::data([
            'type' => 'exercise',
            'needs_weight_input' => $weight === null,
            'exercise' => [
                'id' => $nextExercise->id,
                'title' => $nextExercise->title,
                'description' => $nextExercise->description,
                'image' => $nextExercise->image_url,
                'sets' => $nextExercise->pivot->sets,
                'reps' => $nextExercise->pivot->reps,
                'order_number' => $nextExercise->pivot->order_number,
                'current_weight' => $weight,
                'is_last' => $currentIndex + 1 === $exercises->count() - 1,
                'exercise_number' => $currentIndex + 2,
                'total_exercises' => $exercises->count(),
            ],
        ]);
    }

    public function saveExerciseResult(UserWorkout $userWorkout, Request $request)
    {
        $user = request()->user();

        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }

        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'reaction' => 'required|in:good,normal,bad',
            'weight_used' => 'nullable|numeric|min:0|max:500',
            'sets_completed' => 'nullable|integer|min:0|max:10',
            'reps_completed' => 'nullable|integer|min:0|max:50',
        ]);

        $performanceData = [
            'sets_completed' => $request->sets_completed,
            'reps_completed' => $request->reps_completed,
            'weight_used' => $request->weight_used,
        ];

        // Обрабатываем реакцию через ExerciseLoadService
        $result = $this->exerciseLoadService->processReaction(
            $user,
            $request->exercise_id,
            $request->reaction,
            $userWorkout->id,
            $performanceData
        );

        // Проверяем фазу отдыха и перегенерируем тренировки при необходимости
        if ($result['rest_phase'] && $result['rest_phase']['required']) {
            app(RegenerationController::class)->checkAndRegenerateWorkouts($user, $request->exercise_id);
        }

        return ApiResponse::success('Результат упражнения сохранен', [
            'exercise_result' => $result,
            'next_url' => route('workout-execution.next-exercise', ['userWorkout' => $userWorkout->id]),
        ]);
    }
}
