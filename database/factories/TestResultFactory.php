<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\TestAttempt;
use App\Models\TestingTestExercise;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestResultFactory extends Factory
{
    public function definition(): array
    {
        $user = User::whereHas('role', fn($q) => $q->where('name', 'user'))->inRandomOrder()->first()
            ?? User::factory()->user()->create();

        $testingTestExercise = TestingTestExercise::inRandomOrder()->first()
            ?? TestingTestExercise::factory()->create();

        return [
            'user_id' => $user->id,
            'testing_id' => $testingTestExercise->testing_id,
            'testing_exercise_id' => $testingTestExercise->testing_exercise_id,
            'test_attempt_id' => TestAttempt::factory(),
            'result_value' => fake()->numberBetween(1, 4),
            'test_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
