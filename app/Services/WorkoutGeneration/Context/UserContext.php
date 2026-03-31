<?php

namespace App\Services\WorkoutGeneration\Context;

use App\Models\User;
use App\Models\UserParameter;
use App\Models\UserProgress;
use App\Models\TestResult;
use App\Models\TestAttempt;
use App\Services\ExerciseLoadService;
use Illuminate\Support\Facades\DB;

//Этот класс собирает и предоставляет все необходимые данные о пользователе для генерации тренировок!
class UserContext
{
    public User $user;
    public ?UserParameter $parameters;
    public ?UserProgress $currentProgress;
    public ?int $lastTestPulse;
    /** @var array<int, int> testing_exercise_id => result_value */
    public array $testResults = [];
    protected ExerciseLoadService $exerciseLoadService;

    public function __construct(User $user, ExerciseLoadService $exerciseLoadService)
    {
        $this->user = $user;
        $this->exerciseLoadService = $exerciseLoadService;
        $this->parameters = $user->userParameters;
        $this->currentProgress = $user->currentProgress();
        $this->loadTestData();
    }

    protected function loadTestData(): void
    {
        // Последние результаты тестов (по одному на тестовое упражнение)
        $this->testResults = TestResult::where('user_id', $this->user->id)
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('test_results')
                    ->where('user_id', $this->user->id)
                    ->groupBy('testing_exercise_id');
            })
            ->with('testingExercise') // Убираем 'exercise' из eager loading
            ->get()
            ->mapWithKeys(function ($result) {
                // Теперь ключом является ID тестового упражнения
                return [$result->testing_exercise_id => $result->result_value];
            })
            ->toArray();

        // Последний пульс с теста
        $lastAttempt = TestAttempt::whereHas('testResults', fn($q) => $q->where('user_id', $this->user->id))
            ->latest('completed_at')
            ->first();
        $this->lastTestPulse = $lastAttempt?->pulse;
    }

    /**
     * Получить результат теста по ID тестового упражнения
     */
    public function getTestResult(int $testingExerciseId): ?int
    {
        return $this->testResults[$testingExerciseId] ?? null;
    }

    /**
     * Получить все результаты тестов
     */
    public function getAllTestResults(): array
    {
        return $this->testResults;
    }

    public function getReactionHistory(int $exerciseId, int $days = 30): \Illuminate\Support\Collection
    {
        return $this->exerciseLoadService->getReactionHistory($this->user->id, $exerciseId, $days);
    }

    public function getUserExerciseWeight(int $exerciseId): ?float
    {
        return $this->exerciseLoadService->getUserCurrentWeight($this->user->id, $exerciseId);
    }
}
