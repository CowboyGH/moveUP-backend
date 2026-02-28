<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProgressFactory extends Factory
{
    public function definition(): array
    {
        static $userProgressCreated = [];

        $user = User::whereDoesntHave('userProgress')
            ->whereHas('role', function($q) {
                $q->where('name', 'user');
            })
            ->inRandomOrder()
            ->first() ?? User::factory()->user()->create();

        $phase = Phase::inRandomOrder()->first() ?? Phase::factory()->create();
        $startDate = fake()->dateTimeBetween('-60 days', 'now');
        $streakDays = fake()->numberBetween(1, 30);
        $completedWorkouts = fake()->numberBetween(1, $phase->min_workouts * 2);

        return [
            'user_id' => $user->id,
            'phase_id' => $phase->id,
            'streak_days' => $streakDays,
            'completed_workouts' => $completedWorkouts,
            'created_at' => $startDate,
            'updated_at' => $startDate,
        ];
    }
}
