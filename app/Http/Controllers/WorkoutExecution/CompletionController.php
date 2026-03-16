<?php

namespace App\Http\Controllers\WorkoutExecution;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompletionController extends BaseWorkoutController
{
    public function complete(UserWorkout $userWorkout)
    {
        $user = request()->user();

        if ($error = $this->checkOwnership($userWorkout)) {
            return $error;
        }
        if ($userWorkout->status === 'completed') {
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Тренировка уже завершена',
                409
            );
        }

        try {
            DB::beginTransaction();
            $userWorkout->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            // Обновляем прогресс и проверяем переход на следующую фазу
            $this->phaseService->handleWorkoutCompletion($userWorkout);

            DB::commit();

            return ApiResponse::success('Тренировка успешно завершена!', [
                'user_workout' => [
                    'id' => $userWorkout->id,
                    'completed_at' => $userWorkout->completed_at,
                ],
                'phase_progress' => $user->currentProgress(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при завершении тренировки: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'workout_id' => $userWorkout->id,
                'error' => $e->getTraceAsString()
            ]);

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при завершении тренировки',
                500
            );
        }
    }
}
