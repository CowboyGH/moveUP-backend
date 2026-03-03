<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exercise\ReactToExerciseRequest;
use App\Http\Requests\Exercise\GetLoadRecommendationRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\UserWorkout;
use App\Services\ExerciseLoadService;
use Illuminate\Http\JsonResponse;

class ExerciseReactionController extends Controller
{
    protected ExerciseLoadService $exerciseLoadService;

    public function __construct(ExerciseLoadService $exerciseLoadService)
    {
        $this->exerciseLoadService = $exerciseLoadService;
    }

    /**
     * Оценка выполненного упражнения
     */
    public function react(ReactToExerciseRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Проверяем, что тренировка принадлежит пользователю
        $userWorkout = UserWorkout::where('id', $validated['user_workout_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$userWorkout) {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Тренировка не принадлежит текущему пользователю',
                403
            );
        }

        // Подготавливаем данные о производительности
        $performanceData = [
            'sets_completed' => $validated['sets_completed'] ?? null,
            'reps_completed' => $validated['reps_completed'] ?? null,
            'weight_used' => $validated['weight_used'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        // Обрабатываем оценку
        $result = $this->exerciseLoadService->processReaction(
            $user,
            $validated['exercise_id'],
            $validated['reaction'],
            $validated['user_workout_id'],
            $performanceData
        );

        return ApiResponse::success('Оценка упражнения сохранена', $result);
    }

    /**
     * Получить историю оценок для упражнения
     */
    public function history(int $exerciseId): JsonResponse
    {
        $user = request()->user();

        $history = $this->exerciseLoadService->getReactionHistory(
            $user->id,
            $exerciseId,
            30 // последние 30 дней
        );

        $analysis = $this->exerciseLoadService->analyzeReactionPattern($history);

        return ApiResponse::data([
            'history' => $history,
            'analysis' => $analysis,
        ]);
    }

    /**
     * Получить рекомендацию по нагрузке для упражнения
     */
    public function recommendation(GetLoadRecommendationRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $recommendation = $this->exerciseLoadService->getLoadRecommendation(
            $user,
            $validated['exercise_id']
        );

        return ApiResponse::data($recommendation);
    }

    /**
     * Получить статистику по всем упражнениям пользователя
     */
    public function statistics(): JsonResponse
    {
        $user = request()->user();

        // Получаем все оценки пользователя
        $reactions = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->with('exercise')
            ->orderBy('reaction_date', 'desc')
            ->get()
            ->groupBy('exercise_id');

        $statistics = [];
        foreach ($reactions as $exerciseId => $exerciseReactions) {
            $analysis = $this->exerciseLoadService->analyzeReactionPattern($exerciseReactions);
            $exercise = $exerciseReactions->first()->exercise;

            $statistics[] = [
                'exercise_id' => $exerciseId,
                'exercise_name' => $exercise ? $exercise->title : 'Упражнение удалено',
                'total_reactions' => $exerciseReactions->count(),
                'last_reaction' => $exerciseReactions->first()->reaction,
                'last_reaction_date' => $exerciseReactions->first()->reaction_date->format('Y-m-d'),
                'analysis' => $analysis,
            ];
        }

        // Общая статистика
        $totalReactions = \App\Models\ExerciseReaction::where('user_id', $user->id)->count();
        $goodCount = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->where('reaction', 'good')
            ->count();
        $badCount = \App\Models\ExerciseReaction::where('user_id', $user->id)
            ->where('reaction', 'bad')
            ->count();
        $normalCount = $totalReactions - $goodCount - $badCount;

        return ApiResponse::data([
            'summary' => [
                'total_reactions' => $totalReactions,
                'good_percentage' => $totalReactions > 0 ? round(($goodCount / $totalReactions) * 100) : 0,
                'normal_percentage' => $totalReactions > 0 ? round(($normalCount / $totalReactions) * 100) : 0,
                'bad_percentage' => $totalReactions > 0 ? round(($badCount / $totalReactions) * 100) : 0,
                'exercises_with_reactions' => $reactions->count(),
            ],
            'exercises' => $statistics,
        ]);
    }
}
