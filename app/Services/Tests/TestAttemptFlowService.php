<?php

namespace App\Services\Tests;

use App\Models\Testing;
use App\Models\TestingExercise;

class TestAttemptFlowService
{
    public function firstExercise(Testing $testing): ?TestingExercise
    {
        return $testing->testExercises()
            ->orderBy('order_number')
            ->first();
    }

    public function nextExercise(Testing $testing, array $completedExerciseIds): ?TestingExercise
    {
        return $testing->testExercises()
            ->whereNotIn('testing_exercises.id', $completedExerciseIds)
            ->orderBy('order_number')
            ->first();
    }

    public function exerciseBelongsToTesting(Testing $testing, int $exerciseId): bool
    {
        return $testing->testExercises()
            ->where('testing_exercises.id', $exerciseId)
            ->exists();
    }

    public function totalExercises(Testing $testing): int
    {
        return $testing->testExercises()->count();
    }

    public function newGuestAttemptData(Testing $testing, string $attemptId): array
    {
        return [
            'attempt_id' => $attemptId,
            'testing_id' => $testing->id,
            'started_at' => now()->toDateTimeString(),
            'status' => 'started',
            'completed_exercises' => [],
            'results' => [],
        ];
    }

    public function resultAlreadySaved(array $completedExerciseIds, int $exerciseId): bool
    {
        return in_array($exerciseId, $completedExerciseIds);
    }

    public function remainingExercises(Testing $testing, int $completedExercises): int
    {
        return max(0, $this->totalExercises($testing) - $completedExercises);
    }

    public function canComplete(Testing $testing, int $completedExercises): bool
    {
        return $this->remainingExercises($testing, $completedExercises) === 0;
    }

    public function startPayload(Testing $testing, int|string $attemptId, TestingExercise $firstExercise): array
    {
        return [
            'attempt_id' => $attemptId,
            'testing' => [
                'id' => $testing->id,
                'title' => $testing->title,
                'description' => $testing->description,
                'duration_minutes' => $testing->duration_minutes,
                'image' => $testing->image,
                'total_exercises' => $this->totalExercises($testing),
            ],
            'current_exercise' => $this->exercisePayload($firstExercise),
        ];
    }

    public function resultPayload(mixed $result, ?TestingExercise $nextExercise): array
    {
        $payload = [
            'saved' => true,
            'result' => $result,
        ];

        if ($nextExercise) {
            $payload['next_exercise'] = $this->exercisePayload($nextExercise);
        } else {
            $payload['all_exercises_completed'] = true;
            $payload['message'] = 'Все упражнения выполнены. Введите пульс для завершения теста.';
        }

        return $payload;
    }

    public function completePayload(int|string $attemptId, mixed $completedAt, int $pulse): array
    {
        return [
            'attempt_id' => $attemptId,
            'completed_at' => $completedAt,
            'pulse' => $pulse,
        ];
    }

    private function exercisePayload(TestingExercise $exercise): array
    {
        return [
            'id' => $exercise->id,
            'description' => $exercise->description,
            'image' => $exercise->image,
            'order_number' => $exercise->pivot->order_number,
        ];
    }
}
