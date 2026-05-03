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
