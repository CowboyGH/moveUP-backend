<?php

namespace App\Services\Profile;

use App\Models\Exercise;
use App\Models\ExercisePerformance;
use App\Models\ExerciseReaction;
use App\Models\User;
use App\Models\UserWorkout;
use App\Services\PhaseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfileStatisticsService
{
    public function __construct(
        private readonly PhaseService $phaseService
    ) {}

    public function overview(User $user, ?int $exerciseId = null, int $weekOffset = 0, ?int $workoutId = null): array
    {
        return [
            'current_phase' => $this->phaseService->getUserPhaseProgress($user),
            'volume' => $this->getVolumeStatistics($user, $exerciseId, $weekOffset),
            'trend' => $this->getTrendStatistics($user, $workoutId),
            'frequency' => $this->getFrequencyStatistics($user),
        ];
    }

    public function volume(User $user, ?int $exerciseId = null, int $weekOffset = 0): array
    {
        return $this->getVolumeStatistics($user, $exerciseId, $weekOffset);
    }

    public function trend(User $user, ?int $workoutId = null): array
    {
        return $this->getTrendStatistics($user, $workoutId);
    }

    public function frequency(User $user, string $period = 'month', int $offset = 0): array
    {
        return $this->getFrequencyStatistics($user, $period, $offset);
    }

    public function workouts(User $user): Collection
    {
        return $user->userWorkouts()
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
    }

    public function exercises(User $user): Collection
    {
        return ExercisePerformance::whereHas('userWorkout', function ($query) use ($user) {
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
    }

    private function getVolumeStatistics(User $user, ?int $exerciseId = null, int $weekOffset = 0): array
    {
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

        if (!$exerciseId) {
            return $this->emptyVolumeStats($weekOffset);
        }

        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);

        $performances = ExercisePerformance::where('exercise_id', $exerciseId)
            ->whereHas('userWorkout', function ($query) use ($user, $startOfWeek, $endOfWeek) {
                $query->where('user_id', $user->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            })
            ->with('userWorkout')
            ->get();

        $exercise = Exercise::find($exerciseId);
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
            $dayKey = $dayMap[$createdAt->dayOfWeekIso] ?? null;

            if ($dayKey) {
                $volume = ($performance->weight_used ?? 0) *
                    ($performance->reps_completed ?? 0) *
                    ($performance->sets_completed ?? 1);

                $daysOfWeek[$dayKey]['total_volume'] += $volume;
                $daysOfWeek[$dayKey]['date'] = $createdAt->format('Y-m-d');

                $totalVolume += $volume;
                $workoutCount++;
            }
        }

        $averageScore = $this->calculateAverageScore($user, $exerciseId);
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
            'chart' => array_values($daysOfWeek),
        ];
    }

    private function getTrendStatistics(User $user, ?int $workoutId = null): array
    {
        $query = $user->userWorkouts()
            ->with(['workout', 'exercisePerformances' => function ($query) {
                $query->with('exercise')->orderBy('id');
            }])
            ->where('status', 'completed')
            ->whereNotNull('completed_at');

        $userWorkout = $workoutId
            ? $query->where('id', $workoutId)->first()
            : $query->latest('completed_at')->first();

        if (!$userWorkout) {
            return [
                'has_data' => false,
                'message' => 'Нет завершенных тренировок',
                'workout' => null,
                'chart' => [],
                'available_workouts' => [],
            ];
        }

        $sortedPerformances = $userWorkout->exercisePerformances
            ->sortBy(function ($performance) use ($userWorkout) {
                $exercise = $userWorkout->workout->exercises
                    ->where('id', $performance->exercise_id)
                    ->first();

                return $exercise?->pivot->order_number ?? 0;
            })
            ->values();

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

    private function getFrequencyStatistics(User $user, string $period = 'month', int $offset = 0): array
    {
        $completedWorkouts = $user->userWorkouts()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->get();

        $periodData = $this->getPeriodData($user, $period, $offset);
        $weeksWithData = $periodData->filter(fn($item) => ($item['count'] ?? 0) > 0)->count();

        $averagePerWeek = $weeksWithData > 0
            ? round($periodData->sum('count') / $weeksWithData, 1)
            : 0;

        $currentProgress = $user->currentProgress();
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
                'total_workouts' => $completedWorkouts->count(),
                'average_per_week' => $averagePerWeek,
                'current_streak' => $this->calculateCurrentStreak($completedWorkouts),
                'longest_streak' => $this->calculateLongestStreak($completedWorkouts),
                'weekly_goal' => $currentProgress?->weekly_workout_goal ?? 4,
            ],
            'chart' => $periodData->values()->toArray(),
        ];
    }

    private function getPeriodData(User $user, string $period, int $offset): Collection
    {
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

    private function getWeeklyData(User $user, int $weeksCount, int $offset): Collection
    {
        $weeks = collect();
        $now = Carbon::now();
        $weekOffset = $weeksCount * $offset;

        for ($i = $weeksCount - 1; $i >= 0; $i--) {
            $startOfWeek = $now->copy()
                ->subWeeks($i + $weekOffset)
                ->startOfWeek();
            $endOfWeek = $now->copy()
                ->subWeeks($i + $weekOffset)
                ->endOfWeek();

            $count = $user->userWorkouts()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
                ->count();

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

    private function getDailyData(User $user, int $offset): Collection
    {
        $days = collect();
        $startOfWeek = Carbon::now()->copy()->subWeeks($offset)->startOfWeek();
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
        }

        return "{$offset} {$periodName} назад";
    }

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

    private function reactionToScore(?string $reaction): float
    {
        return match($reaction) {
            'good' => 100,
            'normal' => 50,
            'bad' => 0,
            default => 50,
        };
    }

    private function scoreToPercent(float $score): int
    {
        return min(100, max(0, (int) $score));
    }

    private function scoreToLabel(float $score): string
    {
        if ($score >= 80) {
            return 'Отлично';
        }
        if ($score >= 50) {
            return 'Нормально';
        }
        if ($score > 0) {
            return 'Ниже среднего';
        }

        return 'Плохо';
    }

    private function calculateCurrentStreak(Collection $workouts): int
    {
        if ($workouts->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = Carbon::today();

        $workoutDays = $workouts->map(function ($workout) {
            return $workout->completed_at->toDateString();
        })->unique()->values();

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

    private function calculateLongestStreak(Collection $workouts): int
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

    private function emptyVolumeStats(int $weekOffset = 0): array
    {
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($weekOffset);
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
