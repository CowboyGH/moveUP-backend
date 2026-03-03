<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workout\CompleteWorkoutWithReactionsRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\WorkoutLoadManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkoutCompletionController extends Controller
{
    protected WorkoutLoadManagerService $loadManager;

    public function __construct(WorkoutLoadManagerService $loadManager)
    {
        $this->loadManager = $loadManager;
    }

    /**
     * Завершить тренировку с оценками и автоматически скорректировать нагрузку
     *
     * @param CompleteWorkoutWithReactionsRequest $request
     * @return JsonResponse
     */
    public function completeWithAdjustments(CompleteWorkoutWithReactionsRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            $result = $this->loadManager->completeWorkoutWithLoadAdjustment(
                $user,
                $validated['workout_id'],
                $validated['reactions']
            );

            return ApiResponse::success(
                'Тренировка успешно завершена. Нагрузка скорректирована для следующих тренировок.',
                $result
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Активная тренировка не найдена',
                404
            );
        } catch (\Exception $e) {
            Log::error('Ошибка при завершении тренировки: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'workout_id' => $validated['workout_id'],
                'error' => $e->getTraceAsString()
            ]);

            return ApiResponse::error(
                ErrorResponse::SERVER_ERROR,
                'Ошибка при обработке тренировки',
                500
            );
        }
    }
}
