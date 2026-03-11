<?php

namespace Database\Factories;

use App\Models\TestAttempt;
use App\Models\Testing;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestAttemptFactory extends Factory
{
    protected $model = TestAttempt::class;

    public function definition(): array
    {
        $started = fake()->dateTimeBetween('-60 days', '-1 day');
        $completed = (clone $started)->modify('+' . fake()->numberBetween(5, 30) . ' minutes');

        return [
            'testing_id' => Testing::inRandomOrder()->first()?->id ?? Testing::factory(),
            'started_at' => $started,
            'completed_at' => $completed,
            'pulse' => fake()->numberBetween(60, 180),
        ];
    }
}
