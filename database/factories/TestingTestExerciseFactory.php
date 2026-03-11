<?php

namespace Database\Factories;

use App\Models\Testing;
use App\Models\TestingExercise;
use App\Models\TestingTestExercise;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestingTestExerciseFactory extends Factory
{
    protected static array $usedCombinations = [];

    protected static bool $initialized = false;

    protected static function initializeUsedCombinations(): void
    {
        if (self::$initialized) {
            return;
        }

        $existing = TestingTestExercise::all(['testing_id', 'testing_exercise_id']);
        foreach ($existing as $record) {
            self::$usedCombinations[$record->testing_id][$record->testing_exercise_id] = true;
        }

        self::$initialized = true;
    }

    public function definition(): array
    {
        self::initializeUsedCombinations();

        static $orderNumbers = [];

        $testingId = $this->getAvailableTestingId();
        $testingExerciseId = $this->getAvailableExerciseId($testingId);

        if (!isset($orderNumbers[$testingId])) {
            $maxOrder = TestingTestExercise::where('testing_id', $testingId)->max('order_number') ?? 0;
            $orderNumbers[$testingId] = $maxOrder + 1;
        } else {
            $orderNumbers[$testingId]++;
        }

        self::$usedCombinations[$testingId][$testingExerciseId] = true;

        return [
            'testing_id'          => $testingId,
            'testing_exercise_id' => $testingExerciseId,
            'order_number'        => $orderNumbers[$testingId],
        ];
    }

    protected function getAvailableTestingId(): int
    {
        $candidates = [];
        foreach (self::$usedCombinations as $id => $exercises) {
            if (count($exercises) < 4) {
                $candidates[] = $id;
            }
        }
        if (!empty($candidates)) {
            return $candidates[array_rand($candidates)];
        }

        $testing = Testing::withCount('testingTestExercises')
            ->having('testing_test_exercises_count', '<', 4)
            ->inRandomOrder()
            ->first();

        if ($testing) {
            $existingForTesting = TestingTestExercise::where('testing_id', $testing->id)->get();
            foreach ($existingForTesting as $record) {
                self::$usedCombinations[$testing->id][$record->testing_exercise_id] = true;
            }
            return $testing->id;
        }

        return Testing::factory()->create()->id;
    }

    protected function getAvailableExerciseId(int $testingId): int
    {
        $used = self::$usedCombinations[$testingId] ?? [];

        $available = TestingExercise::whereNotIn('id', array_keys($used))
            ->inRandomOrder()
            ->first();

        if ($available) {
            return $available->id;
        }

        return TestingExercise::factory()->create()->id;
    }
}
