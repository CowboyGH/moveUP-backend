<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workout;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserWorkoutFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-30 days', 'now');
        $isCompleted = fake()->boolean(75);

        return [
            'user_id' => User::factory(),
            'workout_id' => Workout::factory(),
            'started_at' => $startedAt,
            'completed_at' => $isCompleted ? fake()->dateTimeBetween($startedAt, 'now') : null,
            'status' => $isCompleted ? 'completed' : 'started',
        ];
    }
}
