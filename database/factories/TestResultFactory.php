<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Testing;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestResult>
 */
class TestResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exercise_id' => Exercise::factory(),
            'testing_id' => Testing::factory(),
            'result_value' => fake()->numberBetween(1, 4),
            'pulse' => fake()->numberBetween(60, 180),
            'test_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'test_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_value' => fake()->numberBetween(85, 100),
        ]);
    }

    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_value' => 4,
        ]);
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_value' => 3,
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_value' => 2,
        ]);
    }
    public function bad(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_value' => 1,
        ]);
    }
}
