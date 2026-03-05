<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProgressFactory extends Factory
{
    public function definition(): array
    {
        $user = User::whereDoesntHave('userProgress')
            ->whereHas('role', function($q) {
                $q->where('name', 'user');
            })
            ->inRandomOrder()
            ->first();

        if (!$user) {
            $user = User::factory()->user()->create();
        }

        $phase = Phase::inRandomOrder()->first() ?? Phase::factory()->create();

        $startDate = fake()->dateTimeBetween('-60 days', 'now');

        $streakDays = fake()->numberBetween(1, 30);

        $weeklyGoal = fake()->numberBetween(1, 7);

        $maxWorkouts = (int) ceil($phase->duration_days / 7 * $weeklyGoal);

        $completedWorkouts = fake()->numberBetween(0, min($maxWorkouts, 50));

        return [
            'user_id' => $user->id,
            'phase_id' => $phase->id,
            'streak_days' => $streakDays,
            'completed_workouts' => $completedWorkouts,
            'weekly_workout_goal' => $weeklyGoal,
            'created_at' => $startDate,
            'updated_at' => $startDate,
        ];
    }
}
