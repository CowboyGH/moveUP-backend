<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProgressFactory extends Factory
{
    public function definition(): array
    {
        $phase = Phase::inRandomOrder()->first() ?? Phase::factory()->create();
        $startDate = fake()->dateTimeBetween('-60 days', 'now');
        $streakDays = fake()->numberBetween(1, 30);
        $completedWorkouts = fake()->numberBetween(1, $phase->min_workouts * 2);

        return [
            'user_id' => User::factory(),
            'phase_id' => $phase->id,
            'streak_days' => $streakDays,
            'completed_workouts' => $completedWorkouts,
            'created_at' => $startDate,
            'updated_at' => $startDate,
        ];
    }

    /**
     * Состояние для нового пользователя (только начал)
     */
    public function newUser(): static
    {
        return $this->state(function (array $attributes) {
            $firstPhase = Phase::orderBy('order_number')->first() ?? Phase::factory()->preparation()->create();

            return [
                'phase_id' => $firstPhase->id,
                'streak_days' => fake()->numberBetween(1, 3),
                'completed_workouts' => fake()->numberBetween(1, 2),
                'created_at' => fake()->dateTimeBetween('-3 days', 'now'),
            ];
        });
    }

    /**
     * Состояние для активного пользователя (регулярно тренируется)
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $phase = Phase::inRandomOrder()->first() ?? Phase::factory()->basic()->create();
            $startDate = fake()->dateTimeBetween('-30 days', '-15 days');

            return [
                'phase_id' => $phase->id,
                'streak_days' => fake()->numberBetween(5, 15),
                'completed_workouts' => fake()->numberBetween(8, 20),
                'created_at' => $startDate,
            ];
        });
    }

    /**
     * Состояние для продвинутого пользователя (давно тренируется)
     */
    public function advanced(): static
    {
        return $this->state(function (array $attributes) {
            $phase = Phase::where('order_number', '>=', 3)->inRandomOrder()->first()
                ?? Phase::factory()->intense()->create();
            $startDate = fake()->dateTimeBetween('-90 days', '-30 days');

            return [
                'phase_id' => $phase->id,
                'streak_days' => fake()->numberBetween(20, 60),
                'completed_workouts' => fake()->numberBetween(30, 80),
                'created_at' => $startDate,
            ];
        });
    }

    /**
     * Состояние для пользователя, который готов перейти на следующую фазу
     */
    public function readyToAdvance(): static
    {
        return $this->state(function (array $attributes) {
            $phase = Phase::where('order_number', '<', 5)->inRandomOrder()->first()
                ?? Phase::factory()->basic()->create();

            $useDaysCondition = fake()->boolean();

            if ($useDaysCondition) {
                $startDate = fake()->dateTimeBetween('-' . ($phase->duration_days + 5) . ' days', '-' . ($phase->duration_days + 1) . ' days');
                $completedWorkouts = fake()->numberBetween(1, $phase->min_workouts - 1);
            } else {
                $startDate = fake()->dateTimeBetween('-' . floor($phase->duration_days / 2) . ' days', 'now');
                $completedWorkouts = $phase->min_workouts + fake()->numberBetween(0, 3);
            }

            return [
                'phase_id' => $phase->id,
                'streak_days' => fake()->numberBetween($phase->duration_days - 3, $phase->duration_days + 5),
                'completed_workouts' => $completedWorkouts,
                'created_at' => $startDate,
            ];
        });
    }

    /**
     * Состояние для пользователя с идеальным посещением (без пропусков)
     */
    public function perfectStreak(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-30 days', '-10 days');
            $daysSinceStart = now()->diffInDays($startDate);

            return [
                'streak_days' => $daysSinceStart,
                'completed_workouts' => fake()->numberBetween($daysSinceStart, $daysSinceStart + 5),
                'created_at' => $startDate,
            ];
        });
    }
    public function forUser(User $user): static
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
    public function forPhase(Phase $phase): static
    {
        return $this->state([
            'phase_id' => $phase->id,
        ]);
    }
}
