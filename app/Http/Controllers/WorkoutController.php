<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Workout;
use App\Models\UserWorkout;
use Illuminate\Http\JsonResponse;

class WorkoutController extends Controller
{
    public function index(): JsonResponse
    {
        $workouts = Workout::where('is_active', 1)
            ->with(['phase', 'exercises', 'warmups'])
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'duration_minutes' => $workout->duration_minutes,
                    'phase' => $workout->phase ? [
                        'id' => $workout->phase->id,
                        'name' => $workout->phase->name,
                    ] : null,
                    'exercises_count' => $workout->exercises->count(),
                    'warmups_count' => $workout->warmups->count(),
                ];
            });

        return ApiResponse::data($workouts);
    }

    public function show(int $id): JsonResponse
    {
        $workout = Workout::where('id', $id)
            ->where('is_active', 1)
            ->with(['phase', 'exercises', 'warmups'])
            ->first();

        if (!$workout) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Тренировка не найдена',
                404
            );
        }

        $formattedExercises = $workout->exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'description' => $exercise->description,
                'image' => $exercise->image,
                'sets' => $exercise->pivot->sets,
                'reps' => $exercise->pivot->reps,
                'order_number' => $exercise->pivot->order_number,
            ];
        })->sortBy('order_number')->values();

        $formattedWarmups = $workout->warmups->map(function ($warmup) {
            return [
                'id' => $warmup->id,
                'name' => $warmup->name,
                'description' => $warmup->description,
                'image' => $warmup->image,
                'order_number' => $warmup->pivot->order_number,
            ];
        })->sortBy('order_number')->values();

        $data = [
            'id' => $workout->id,
            'title' => $workout->title,
            'description' => $workout->description,
            'duration_minutes' => $workout->duration_minutes,
            'phase' => $workout->phase ? [
                'id' => $workout->phase->id,
                'name' => $workout->phase->name,
            ] : null,
            'exercises' => $formattedExercises,
            'warmups' => $formattedWarmups,
        ];

        return ApiResponse::data($data);
    }

    public function myWorkoutHistory(): JsonResponse
    {
        $user = auth()->user();

        $userWorkouts = UserWorkout::with(['workout', 'exercisePerformances', 'userWarmupPerformances.warmup'])
            ->where('user_id', $user->id)
            ->orderBy('started_at', 'desc')->get();

        $activeWorkout = $userWorkouts->where('status', 'in_progress')->first();

        $formattedHistory = $userWorkouts->map(function ($userWorkout) {
            $workout = $userWorkout->workout;

            $totalExercises = $userWorkout->exercisePerformances->count();
            $completedExercises = $userWorkout->exercisePerformances->where('completed', true)->count();

            $totalWarmups = $userWorkout->userWarmupPerformances->count();
            $completedWarmups = $userWorkout->userWarmupPerformances->where('completed', true)->count();

            return [
                'id' => $userWorkout->id,
                'workout' => [
                    'id' => $workout ? $workout->id : null,
                    'title' => $workout ? $workout->title : 'Тренировка удалена',
                ],
                'started_at' => $userWorkout->started_at ? $userWorkout->started_at->format('Y-m-d H:i:s') : null,
                'completed_at' => $userWorkout->completed_at ? $userWorkout->completed_at->format('Y-m-d H:i:s') : null,
                'status' => $userWorkout->status,
                'duration' => $userWorkout->completed_at && $userWorkout->started_at
                    ? (int) $userWorkout->started_at->diffInMinutes($userWorkout->completed_at)
                    : null,
                'progress' => [
                    'exercises_completed' => $completedExercises,
                    'exercises_total' => $totalExercises,
                    'warmups_completed' => $completedWarmups,
                    'warmups_total' => $totalWarmups,
                ],
            ];
        });

        $statistics = [
            'total_workouts_started' => $userWorkouts->count(),
            'total_workouts_completed' => $userWorkouts->where('status', 'completed')->count(),
            'total_workouts_in_progress' => $userWorkouts->where('status', 'in_progress')->count(),
            'last_workout_date' => $userWorkouts->isNotEmpty() && $userWorkouts->first()->completed_at
                ? $userWorkouts->first()->completed_at->format('Y-m-d H:i:s')
                : null,
        ];

        $data = [
            'active' => $activeWorkout ? [
                'id' => $activeWorkout->id,
                'workout_id' => $activeWorkout->workout_id,
                'title' => $activeWorkout->workout ? $activeWorkout->workout->title : 'Тренировка удалена',
                'started_at' => $activeWorkout->started_at->format('Y-m-d H:i:s'),
                'duration_minutes' => (int) $activeWorkout->started_at->diffInMinutes(now()),
            ] : null,
            'statistics' => $statistics,
            'history' => $formattedHistory,
        ];

        return ApiResponse::data($data);
    }

    private function getWorkoutStatus(UserWorkout $userWorkout): string
    {
        return $userWorkout->status;
    }
}
