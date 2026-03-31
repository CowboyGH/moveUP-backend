<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workout\StartWorkoutRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use App\Models\Workout;
use App\Http\Controllers\WorkoutExecution\ExerciseController;
use Illuminate\Http\Request;

class WorkoutStartController extends Controller
{
    public function start(StartWorkoutRequest $request)
    {
        $user = $request->user();
        $workout = Workout::find($request->workout_id);

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

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
                'Тренировка не назначена пользователю',
                404
            );
        }

        // Проверяем, выбрал ли пользователь разминку
        if ($request->has('with_warmup') && $request->with_warmup) {
            $warmups = $userWorkout->workout->warmups()->orderBy('pivot_order_number')->get();

            if ($warmups->isEmpty()) {
                // Если разминки нет, сразу начинаем тренировку
                return $this->startWorkout($userWorkout);
            }

            // Обновляем статус тренировки на started
            $userWorkout->update([
                'status' => UserWorkout::STATUS_STARTED,
                'started_at' => now(),
            ]);

            // Возвращаем первое упражнение разминки
            $firstWarmup = $warmups->first();

            return ApiResponse::success('Разминка начата', [
                'user_workout_id' => $userWorkout->id,
                'type' => 'warmup',
                'warmup' => [
                    'id' => $firstWarmup->id,
                    'name' => $firstWarmup->name,
                    'description' => $firstWarmup->description,
                    'image' => $firstWarmup->image_url,
                    'duration_seconds' => 60,
                    'order_number' => $firstWarmup->pivot->order_number,
                    'is_last' => $warmups->count() === 1,
                ],
                'total_warmups' => $warmups->count(),
            ]);
        }

        // Если разминка не выбрана, сразу начинаем тренировку
        return $this->startWorkout($userWorkout);
    }

    private function startWorkout(UserWorkout $userWorkout)
    {
        $userWorkout->update([
            'status' => UserWorkout::STATUS_STARTED,
            'started_at' => now(),
        ]);

        // Получаем упражнения напрямую
        $workout = $userWorkout->workout()->with('exercises')->first();
        $exercises = $workout->exercises->sortBy('pivot.order_number');
        $firstExercise = $exercises->first();

        if (!$firstExercise) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'В тренировке нет упражнений',
                404
            );
        }

        // Используем метод-геттер вместо прямого доступа к свойству
        $exerciseController = app(ExerciseController::class);
        $weight = $exerciseController->getExerciseLoadService()->getUserCurrentWeight(
            $userWorkout->user_id,
            $firstExercise->id
        );

        return ApiResponse::success('Тренировка начата', [
            'user_workout_id' => $userWorkout->id,
            'started_at' => $userWorkout->started_at,
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

    public function abandon(UserWorkout $userWorkout)
    {
        $user = request()->user();

        if ($userWorkout->user_id !== $user->id) {
            return ApiResponse::error(ErrorResponse::FORBIDDEN, 'Тренировка не принадлежит текущему пользователю', 403);
        }

        if ($userWorkout->status === UserWorkout::STATUS_STARTED) {
            $userWorkout->update(['status' => UserWorkout::STATUS_ASSIGNED]);
            return ApiResponse::success('Тренировка сброшена в статус назначена');
        }

        return ApiResponse::error(ErrorResponse::CONFLICT, 'Тренировка не активна', 409);
    }
}
