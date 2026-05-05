<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Exercise;
use App\Models\ExercisePerformance;
use App\Models\UserWorkout;
use App\Services\Profile\ProfileStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileStatisticsController extends Controller
{
    public function __construct(
        private readonly ProfileStatisticsService $statisticsService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success('Статистика пользователя', $this->statisticsService->overview(
            $request->user(),
            $request->get('exercise_id'),
            $request->get('week_offset', 0),
            $request->get('workout_id')
        ));
    }

    public function volume(Request $request): JsonResponse
    {
        $request->validate([
            'exercise_id' => 'nullable|integer|min:1',
            'week_offset' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $exerciseId = $request->get('exercise_id');

        if ($exerciseId) {
            if (!Exercise::where('id', $exerciseId)->exists()) {
                return ApiResponse::error(
                    ErrorResponse::NOT_FOUND,
                    'Упражнение не найдено',
                    404
                );
            }

            $hasData = ExercisePerformance::where('exercise_id', $exerciseId)
                ->whereHas('userWorkout', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->exists();

            if (!$hasData) {
                return ApiResponse::error(
                    'no_data',
                    'У вас нет данных по этому упражнению',
                    404
                );
            }
        }

        return ApiResponse::data($this->statisticsService->volume(
            $user,
            $exerciseId,
            $request->get('week_offset', 0)
        ));
    }

    public function trend(Request $request): JsonResponse
    {
        $request->validate([
            'workout_id' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $workoutId = $request->get('workout_id');

        if ($workoutId) {
            $workout = UserWorkout::where('id', $workoutId)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->first();

            if (!$workout) {
                return ApiResponse::error(
                    ErrorResponse::NOT_FOUND,
                    'Тренировка не найдена',
                    404
                );
            }
        }

        return ApiResponse::data($this->statisticsService->trend($user, $workoutId));
    }

    public function frequency(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:week,month,3months,6months,year',
            'offset' => 'nullable|integer|min:0',
        ]);

        return ApiResponse::data($this->statisticsService->frequency(
            $request->user(),
            $request->get('period', 'month'),
            $request->get('offset', 0)
        ));
    }

    public function workouts(Request $request): JsonResponse
    {
        return ApiResponse::data($this->statisticsService->workouts($request->user()));
    }

    public function exercises(Request $request): JsonResponse
    {
        return ApiResponse::data($this->statisticsService->exercises($request->user()));
    }
}
