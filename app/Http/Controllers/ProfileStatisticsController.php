<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\Exercise;
use App\Models\ExercisePerformance;
use App\Models\UserExerciseWeight;
use App\Models\ExerciseReaction;
use App\Models\UserWorkout;
use App\Services\PhaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Responses\ErrorResponse;

class ProfileStatisticsController extends Controller
{
    protected PhaseService $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }

    /**
     * Получить всю статистику профиля
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Получаем текущую фазу пользователя
        $phaseProgress = $this->phaseService->getUserPhaseProgress($user);

        // Получаем статистику объема
        $volumeStats = $this->getVolumeStatistics(
            $user,
            $request->get('exercise_id'),
            $request->get('week_offset', 0)
        );

        // Получаем статистику тренда
        $trendStats = $this->getTrendStatistics(
            $user,
            $request->get('workout_id')
        );

        // Получаем статистику частоты
        $frequencyStats = $this->getFrequencyStatistics($user);

        return ApiResponse::success('Статистика пользователя', [
            'current_phase' => $phaseProgress,
            'volume' => $volumeStats,
            'trend' => $trendStats,
            'frequency' => $frequencyStats,
        ]);
    }

    /**
     * Получить статистику объема для конкретного упражнения
     */
    public function volume(Request $request): JsonResponse
    {
        $request->validate([
            'exercise_id' => 'nullable|integer|min:1',
            'week_offset' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $exerciseId = $request->get('exercise_id');
        $weekOffset = $request->get('week_offset', 0);

        // Если exercise_id передан, проверяем его существование и наличие данных
        if ($exerciseId) {
            // Проверяем существование упражнения в таблице exercises
            $exerciseExists = \App\Models\Exercise::where('id', $exerciseId)->exists();

            if (!$exerciseExists) {
                return ApiResponse::error(
                    ErrorResponse::NOT_FOUND,
                    'Упражнение не найдено',
                    404
                );
            }

            // Проверяем, выполнял ли пользователь это упражнение
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

        $volumeStats = $this->getVolumeStatistics($user, $exerciseId, $weekOffset);

        return ApiResponse::data($volumeStats);
    }

    /**
     * Получить статистику тренда для выбранной тренировки
     */
    public function trend(Request $request): JsonResponse
    {
        $request->validate([
            'workout_id' => 'nullable|integer|min:1', // только проверка формата
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

        $trendStats = $this->getTrendStatistics($user, $workoutId);

        return ApiResponse::data($trendStats);
    }

    /**
     * Получить статистику частоты с возможностью выбора периода
     */
    public function frequency(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:week,month,3months,6months,year',
            'offset' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();

        $period = $request->get('period', 'month');
        $offset = $request->get('offset', 0);

        $frequencyStats = $this->getFrequencyStatistics($user, $period, $offset);

        return ApiResponse::data($frequencyStats);
    }

    /**
     * Получить список тренировок для выбора в статистике тренда
     */
    public function workouts(Request $request): JsonResponse
    {
        $user = $request->user();

        $workouts = $user->userWorkouts()
            ->with('workout')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($userWorkout) {
                $completedAt = $this->formatDate($userWorkout->completed_at);

                return [
                    'id' => $userWorkout->id,
                    'workout_id' => $userWorkout->workout->id,
                    'title' => $userWorkout->workout->title,
                    'completed_at' => $completedAt,
                    'completed_at_formatted' => $completedAt ? Carbon::parse($completedAt)->format('d.m.Y') : null,
                    'duration_minutes' => $userWorkout->started_at && $userWorkout->completed_at
                        ? (int) $this->getCarbonInstance($userWorkout->started_at)->diffInMinutes($this->getCarbonInstance($userWorkout->completed_at))
                        : null,
                ];
            });

        return ApiResponse::data($workouts);
    }

    /**
     * Получить список упражнений для выбора в статистике объема
     */
    public function exercises(Request $request): JsonResponse
    {
        $user = $request->user();

        $exercises = ExercisePerformance::whereHas('userWorkout', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('exercise')
            ->select('exercise_id', DB::raw('MAX(created_at) as last_used'))
            ->groupBy('exercise_id')
            ->orderBy('last_used', 'desc')
            ->get()
            ->map(function ($item) {
                $lastUsed = $this->formatDate($item->last_used);

                return [
                    'id' => $item->exercise_id,
                    'name' => $item->exercise->title ?? 'Упражнение удалено',
                    'last_used' => $lastUsed,
                    'last_used_formatted' => $lastUsed ? Carbon::parse($lastUsed)->format('d.m.Y') : null,
                ];
            });

        return ApiResponse::data($exercises);
    }

    /**
     * Получить статистику объема за неделю с учетом смещения
     */
    private function getVolumeStatistics(User $user, ?int $exerciseId = null, int $weekOffset = 0): array
    {
        // Если упражнение не выбрано, берем первое из истории
        if (!$exerciseId) {
            $lastExercise = ExercisePerformance::whereHas('userWorkout', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->select('exercise_id')
                ->groupBy('exercise_id')
                ->orderByRaw('MAX(created_at) DESC')
                ->first();

            $exerciseId = $lastExercise?->exercise_id;
        }

        // Если нет данных по упражнениям, возвращаем пустую статистику
        if (!$exerciseId) {
            return $this->emptyVolumeStats($weekOffset);
        }

        // Вычисляем начало и конец недели с учетом смещения
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);

        // Получаем все выполнения упражнения за указанную неделю
        $performances = ExercisePerformance::where('exercise_id', $exerciseId)
            ->whereHas('userWorkout', function ($query) use ($user, $startOfWeek, $endOfWeek) {
                $query->where('user_id', $user->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            })
            ->with('userWorkout')
            ->get();

        // Получаем информацию об упражнении
        $exercise = Exercise::find($exerciseId);

        // Группируем по дням недели
        $daysOfWeek = $this->getDaysOfWeekArray();

        $dayMap = [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];

        $totalVolume = 0;
        $workoutCount = 0;

        foreach ($performances as $performance) {
            $createdAt = $this->getCarbonInstance($performance->created_at);
            $dayOfWeek = $createdAt->dayOfWeekIso;
            $dayKey = $dayMap[$dayOfWeek] ?? null;

            if ($dayKey) {
                // Объем = вес * повторения * подходы
                $volume = ($performance->weight_used ?? 0) *
                    ($performance->reps_completed ?? 0) *
                    ($performance->sets_completed ?? 1);

                $daysOfWeek[$dayKey]['total_volume'] += $volume;
                $daysOfWeek[$dayKey]['date'] = $createdAt->format('Y-m-d');

                $totalVolume += $volume;
                $workoutCount++;
            }
        }

        // Преобразуем в массив для графика
        $chartData = array_values($daysOfWeek);

        // Средняя оценка упражнения
        $averageScore = $this->calculateAverageScore($user, $exerciseId);

        // Вычисляем номер недели (обратный порядок: 0 = самая новая, 3 = самая старая)
        $weekNumber = 4 - $weekOffset;

        return [
            'has_data' => $workoutCount > 0,
            'exercise' => $exercise ? [
                'id' => $exercise->id,
                'title' => $exercise->title,
                'muscle_group' => $exercise->muscle_group,
            ] : null,
            'average_score' => $averageScore,
            'average_score_percent' => $this->scoreToPercent($averageScore),
            'average_score_label' => $this->scoreToLabel($averageScore),
            'period' => [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
                'label' => "Неделя {$weekNumber}",
                'week_number' => $weekNumber,
                'week_offset' => $weekOffset,
                'can_go_previous' => $this->hasPreviousWeekData($user, $exerciseId, $weekOffset + 1),
                'can_go_next' => $weekOffset > 0 ? $this->hasNextWeekData($user, $exerciseId, $weekOffset - 1) : false,
            ],
            'summary' => [
                'total_volume' => round($totalVolume, 1),
                'workout_count' => $workoutCount,
                'average_volume_per_workout' => $workoutCount > 0 ? round($totalVolume / $workoutCount, 1) : 0,
            ],
            'chart' => $chartData,
        ];
    }

    /**
     * Получить статистику тренда по выбранной тренировке
     */
    private function getTrendStatistics(User $user, ?int $workoutId = null): array
    {
        // Находим тренировку (либо по ID, либо последнюю)
        $query = $user->userWorkouts()
            ->with(['workout', 'exercisePerformances' => function ($query) {
                $query->with('exercise')->orderBy('id');
            }])
            ->where('status', 'completed')
            ->whereNotNull('completed_at');

        if ($workoutId) {
            $userWorkout = $query->where('id', $workoutId)->first();
        } else {
            $userWorkout = $query->latest('completed_at')->first();
        }

        if (!$userWorkout) {
            return [
                'has_data' => false,
                'message' => 'Нет завершенных тренировок',
                'workout' => null,
                'chart' => [],
                'available_workouts' => [],
            ];
        }

        // Получаем все выполнения упражнений из этой тренировки
        $performances = $userWorkout->exercisePerformances;

        // Сортируем по порядку в тренировке
        $sortedPerformances = $performances->sortBy(function ($performance) use ($userWorkout) {
            $exercise = $userWorkout->workout->exercises
                ->where('id', $performance->exercise_id)
                ->first();
            return $exercise?->pivot->order_number ?? 0;
        })->values();

        // Формируем данные для графика
        $chartData = [];
        $averageScoreSum = 0;
        $scoreCount = 0;

        foreach ($sortedPerformances as $index => $performance) {
            $score = $this->reactionToScore($performance->reaction);
            $averageScoreSum += $score;
            $scoreCount++;

            $chartData[] = [
                'exercise_number' => $index + 1,
                'exercise_id' => $performance->exercise_id,
                'exercise_name' => $performance->exercise->title ?? 'Упражнение',
                'reaction' => $performance->reaction,
                'score' => $score,
                'score_percent' => $this->scoreToPercent($score),
                'score_label' => $this->scoreToLabel($score),
                'weight_used' => $performance->weight_used,
                'sets_completed' => $performance->sets_completed,
                'reps_completed' => $performance->reps_completed,
                'sets_planned' => $performance->sets_planned,
                'reps_planned' => $performance->reps_planned,
            ];
        }

        $averageScore = $scoreCount > 0 ? round($averageScoreSum / $scoreCount, 1) : 0;

        return [
            'has_data' => true,
            'workout' => [
                'id' => $userWorkout->id,
                'workout_id' => $userWorkout->workout->id,
                'title' => $userWorkout->workout->title,
                'completed_at' => $this->formatDate($userWorkout->completed_at),
                'completed_at_formatted' => $this->formatDate($userWorkout->completed_at, 'd.m.Y H:i'),
                'duration_minutes' => $userWorkout->started_at && $userWorkout->completed_at
                    ? (int) $this->getCarbonInstance($userWorkout->started_at)->diffInMinutes($this->getCarbonInstance($userWorkout->completed_at))
                    : null,
            ],
            'average_score' => $averageScore,
            'average_score_percent' => $this->scoreToPercent($averageScore),
            'average_score_label' => $this->scoreToLabel($averageScore),
            'chart' => $chartData,
            'available_workouts' => $this->getAvailableWorkouts($user, $userWorkout->id),
        ];
    }

    /**
     * Получить статистику частоты тренировок за указанный период
     */
    private function getFrequencyStatistics(User $user, string $period = 'month', int $offset = 0): array
    {
        // Получаем все завершенные тренировки
        $completedWorkouts = $user->userWorkouts()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->get();

        // Получаем данные за указанный период
        $periodData = $this->getPeriodData($user, $period, $offset);

        // Вычисляем общую статистику
        $totalWorkouts = $completedWorkouts->count();
        $weeksWithData = $periodData->filter(fn($item) => ($item['count'] ?? 0) > 0)->count();

        // Среднее количество тренировок в неделю
        $averagePerWeek = $weeksWithData > 0
            ? round($periodData->sum('count') / $weeksWithData, 1)
            : 0;

        // Текущая серия
        $currentStreak = $this->calculateCurrentStreak($completedWorkouts);

        // Максимальная серия
        $longestStreak = $this->calculateLongestStreak($completedWorkouts);

        // Недельная цель из прогресса пользователя
        $currentProgress = $user->currentProgress();
        $weeklyGoal = $currentProgress?->weekly_workout_goal ?? 4;

        $hasData = $periodData->sum('count') > 0;

        return [
            'has_data' => $hasData,
            'period_info' => [
                'type' => $period,
                'offset' => $offset,
                'label' => $this->getPeriodLabel($period, $offset),
                'items_count' => $periodData->count(),
            ],
            'summary' => [
                'total_workouts' => $totalWorkouts,
                'average_per_week' => $averagePerWeek,
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'weekly_goal' => $weeklyGoal,
            ],
            'chart' => $periodData->values()->toArray(),
        ];
    }

    /**
     * Получить данные за указанный период
     */
    private function getPeriodData(User $user, string $period, int $offset): \Illuminate\Support\Collection
    {
        $now = Carbon::now();

        switch ($period) {
            case 'week':
                return $this->getDailyData($user, $offset);

            case 'month':
                $weeksCount = 4;
                break;

            case '3months':
                $weeksCount = 12;
                break;

            case '6months':
                $weeksCount = 24;
                break;

            case 'year':
                $weeksCount = 52;
                break;

            default:
                $weeksCount = 4;
        }

        return $this->getWeeklyData($user, $weeksCount, $offset);
    }

    /**
     * Получить данные по неделям
     */
    private function getWeeklyData(User $user, int $weeksCount, int $offset): \Illuminate\Support\Collection
    {
        $weeks = collect();
        $now = Carbon::now();

        // Смещение: умножаем количество недель на offset
        $weekOffset = $weeksCount * $offset;

        // Проходим по неделям (от самой старой к самой новой)
        for ($i = $weeksCount - 1; $i >= 0; $i--) {
            $startOfWeek = $now->copy()
                ->subWeeks($i + $weekOffset)
                ->startOfWeek();
            $endOfWeek = $now->copy()
                ->subWeeks($i + $weekOffset)
                ->endOfWeek();

            // Считаем тренировки за эту неделю
            $count = $user->userWorkouts()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Формируем подпись для недели - только числа
            $weekNumber = $weeksCount - $i;

            $weeks->push([
                'week_index' => $i,
                'week_number' => $weekNumber,
                'label' => "Нед {$weekNumber}",
                'short_label' => (string) $weekNumber,
                'start_date' => $startOfWeek->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
                'count' => $count,
                'goal' => $user->currentProgress()?->weekly_workout_goal ?? 4,
            ]);
        }

        return $weeks;
    }

    /**
     * Получить данные по дням (для недельного периода)
     */
    private function getDailyData(User $user, int $offset): \Illuminate\Support\Collection
    {
        $days = collect();
        $now = Carbon::now();

        $startOfWeek = $now->copy()->subWeeks($offset)->startOfWeek();
        $endOfWeek = $now->copy()->subWeeks($offset)->endOfWeek();

        $dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $count = $user->userWorkouts()
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->count();

            $days->push([
                'day_index' => $i,
                'day_number' => $i + 1,
                'label' => $dayNames[$i],
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('d.m'),
                'count' => $count,
                'goal' => null,
            ]);
        }

        return $days;
    }

    /**
     * Получить доступные тренировки для переключения
     */
    private function getAvailableWorkouts(User $user, int $currentWorkoutId): array
    {
        $workouts = $user->userWorkouts()
            ->with('workout')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($workout) use ($currentWorkoutId) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->workout->title,
                    'date' => $this->formatDate($workout->completed_at, 'd.m.Y'),
                    'is_current' => $workout->id === $currentWorkoutId,
                ];
            });

        return $workouts->toArray();
    }

    /**
     * Проверить, есть ли данные за предыдущую неделю
     */
    private function hasPreviousWeekData(User $user, int $exerciseId, int $weekOffset): bool
    {
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);

        return ExercisePerformance::where('exercise_id', $exerciseId)
            ->whereHas('userWorkout', function ($query) use ($user, $startOfWeek, $endOfWeek) {
                $query->where('user_id', $user->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            })
            ->exists();
    }

    /**
     * Проверить, есть ли данные за следующую неделю
     */
    private function hasNextWeekData(User $user, int $exerciseId, int $weekOffset): bool
    {
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);

        return ExercisePerformance::where('exercise_id', $exerciseId)
            ->whereHas('userWorkout', function ($query) use ($user, $startOfWeek, $endOfWeek) {
                $query->where('user_id', $user->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            })
            ->exists();
    }

    /**
     * Получить массив дней недели
     */
    private function getDaysOfWeekArray(): array
    {
        return [
            'monday' => ['name' => 'Пн', 'total_volume' => 0, 'date' => null],
            'tuesday' => ['name' => 'Вт', 'total_volume' => 0, 'date' => null],
            'wednesday' => ['name' => 'Ср', 'total_volume' => 0, 'date' => null],
            'thursday' => ['name' => 'Чт', 'total_volume' => 0, 'date' => null],
            'friday' => ['name' => 'Пт', 'total_volume' => 0, 'date' => null],
            'saturday' => ['name' => 'Сб', 'total_volume' => 0, 'date' => null],
            'sunday' => ['name' => 'Вс', 'total_volume' => 0, 'date' => null],
        ];
    }

    /**
     * Получить подпись для периода
     */
    private function getPeriodLabel(string $period, int $offset): string
    {
        $periodNames = [
            'week' => 'неделя',
            'month' => 'месяц',
            '3months' => '3 месяца',
            '6months' => '6 месяцев',
            'year' => 'год',
        ];

        $periodName = $periodNames[$period] ?? $period;

        if ($offset === 0) {
            return "Текущий {$periodName}";
        } elseif ($offset === 1) {
            return "Прошлый {$periodName}";
        } else {
            return "{$offset} {$periodName} назад";
        }
    }

    /**
     * Форматирование даты
     */
    private function formatDate($date, string $format = 'Y-m-d'): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return $this->getCarbonInstance($date)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Вспомогательный метод для получения Carbon instance
     */
    private function getCarbonInstance($date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }
        if ($date instanceof \DateTime) {
            return Carbon::instance($date);
        }
        if (is_string($date)) {
            return Carbon::parse($date);
        }
        return Carbon::now();
    }

    /**
     * Рассчитать среднюю оценку для упражнения
     */
    private function calculateAverageScore(User $user, int $exerciseId): float
    {
        $reactions = ExerciseReaction::where('user_id', $user->id)
            ->where('exercise_id', $exerciseId)
            ->orderBy('reaction_date', 'desc')
            ->limit(10)
            ->get();

        if ($reactions->isEmpty()) {
            return 0;
        }

        $sum = 0;
        foreach ($reactions as $reaction) {
            $sum += $this->reactionToScore($reaction->reaction);
        }

        return round($sum / $reactions->count(), 1);
    }

    /**
     * Конвертировать реакцию в числовую оценку
     */
    private function reactionToScore(?string $reaction): float
    {
        return match($reaction) {
            'good' => 100,
            'normal' => 50,
            'bad' => 0,
            default => 50,
        };
    }

    /**
     * Конвертировать оценку в проценты
     */
    private function scoreToPercent(float $score): int
    {
        return min(100, max(0, (int) $score));
    }

    /**
     * Конвертировать оценку в текстовую метку
     */
    private function scoreToLabel(float $score): string
    {
        if ($score >= 80) return 'Отлично';
        if ($score >= 50) return 'Нормально';
        if ($score > 0) return 'Ниже среднего';
        return 'Плохо';
    }

    /**
     * Рассчитать текущую серию тренировок (дни подряд)
     */
    private function calculateCurrentStreak(\Illuminate\Support\Collection $workouts): int
    {
        if ($workouts->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = Carbon::today();

        // Группируем по дням
        $workoutDays = $workouts->map(function ($workout) {
            return $workout->completed_at->toDateString();
        })->unique()->values();

        // Идем от сегодня назад
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->subDays($i)->toDateString();

            if ($workoutDays->contains($date)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Рассчитать максимальную серию
     */
    private function calculateLongestStreak(\Illuminate\Support\Collection $workouts): int
    {
        if ($workouts->isEmpty()) {
            return 0;
        }

        $longestStreak = 0;
        $currentStreak = 0;
        $lastDate = null;

        $workoutDays = $workouts->map(function ($workout) {
            return $workout->completed_at->toDateString();
        })->unique()->sort()->values();

        foreach ($workoutDays as $date) {
            if ($lastDate) {
                $diff = Carbon::parse($date)->diffInDays(Carbon::parse($lastDate));

                if ($diff == 1) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }
            } else {
                $currentStreak = 1;
            }

            $longestStreak = max($longestStreak, $currentStreak);
            $lastDate = $date;
        }

        return $longestStreak;
    }

    /**
     * Пустая статистика объема
     */
    private function emptyVolumeStats(int $weekOffset = 0): array
    {
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);

        // Вычисляем номер недели
        $weekNumber = 4 - $weekOffset;

        return [
            'has_data' => false,
            'exercise' => null,
            'average_score' => 0,
            'average_score_percent' => 0,
            'average_score_label' => 'Нет данных',
            'period' => [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
                'label' => "Неделя {$weekNumber}",
                'week_number' => $weekNumber,
                'week_offset' => $weekOffset,
                'can_go_previous' => false,
                'can_go_next' => $weekOffset > 0,
            ],
            'summary' => [
                'total_volume' => 0,
                'workout_count' => 0,
                'average_volume_per_workout' => 0,
            ],
            'chart' => array_values($this->getDaysOfWeekArray()),
        ];
    }
}
