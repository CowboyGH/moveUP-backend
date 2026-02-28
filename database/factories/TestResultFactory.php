<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Testing;
use App\Models\TestingExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestResultFactory extends Factory
{
    public function definition(): array
    {
        static $userTestCounts = [];

        $user = User::whereHas('role', function($q) {
            $q->where('name', 'user');
        })->inRandomOrder()->first() ?? User::factory()->user()->create();

        $testCount = $userTestCounts[$user->id] ?? 0;
        if ($testCount >= 3) {
            $user = User::whereHas('role', function($q) {
                $q->where('name', 'user');
            })->whereDoesntHave('testResults', function($q) {
                $q->groupBy('user_id')
                    ->havingRaw('COUNT(*) < 3');
            })->inRandomOrder()->first() ?? User::factory()->user()->create();

            $userTestCounts[$user->id] = $userTestCounts[$user->id] ?? 0;
        }

        $userTestCounts[$user->id] = ($userTestCounts[$user->id] ?? 0) + 1;

        return [
            'user_id' => $user->id,
            'exercise_id' => TestingExercise::inRandomOrder()->first()->id
                ?? TestingExercise::factory()->create()->id,
            'testing_id' => Testing::inRandomOrder()->first()->id
                ?? Testing::factory()->create()->id,
            'result_value' => fake()->numberBetween(1, 4),
            'pulse' => fake()->numberBetween(60, 180),
            'test_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
