<?php

namespace Database\Factories;

use App\Models\Testing;
use App\Models\TestingExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestingTestExerciseFactory extends Factory
{
    public function definition(): array
    {
        static $testExerciseCounts = [];
        static $orderNumbers = [];

        $testingId = Testing::inRandomOrder()->first()->id
            ?? Testing::factory()->create()->id;

        $currentCount = $testExerciseCounts[$testingId] ?? 0;

        if ($currentCount >= 6) {
            $testingId = Testing::whereDoesntHave('testingTestExercises', function($q) {
                $q->groupBy('testing_id')
                    ->havingRaw('COUNT(*) < 6');
            })->inRandomOrder()->first()?->id
                ?? Testing::factory()->create()->id;
        }

        $testExerciseCounts[$testingId] = ($testExerciseCounts[$testingId] ?? 0) + 1;

        if (!isset($orderNumbers[$testingId])) {
            $orderNumbers[$testingId] = 1;
        } else {
            $orderNumbers[$testingId]++;
        }

        return [
            'testing_id' => $testingId,
            'testing_exercise_id' => TestingExercise::inRandomOrder()->first()->id
                ?? TestingExercise::factory()->create()->id,
            'order_number' => $orderNumbers[$testingId],
        ];
    }
}
