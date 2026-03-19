<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workout;
use App\Models\UserWorkout;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserWorkoutFactory extends Factory
{
    protected $model = UserWorkout::class;

    public function definition(): array
    {
        // ВАЖНО: берем существующие тренировки, а не создаем новые
        $workout = Workout::inRandomOrder()->first();

        // Если нет тренировок, создаем одну (но это крайний случай)
        if (!$workout) {
            $workout = Workout::factory()->create();
        }

        $status = $this->faker->randomElement([
            UserWorkout::STATUS_ASSIGNED,
            UserWorkout::STATUS_STARTED,
            UserWorkout::STATUS_COMPLETED,
        ]);

        $startedAt = null;
        $completedAt = null;

        if ($status === UserWorkout::STATUS_STARTED || $status === UserWorkout::STATUS_COMPLETED) {
            $startedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        }

        if ($status === UserWorkout::STATUS_COMPLETED) {
            $completedAt = $this->faker->dateTimeBetween($startedAt ?? '-30 days', 'now');
        }

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'workout_id' => $workout->id, // Используем существующую тренировку
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'status' => $status,
        ];
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserWorkout::STATUS_ASSIGNED,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserWorkout::STATUS_STARTED,
            'started_at' => now()->subHours(rand(1, 48)),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserWorkout::STATUS_COMPLETED,
            'started_at' => now()->subDays(rand(1, 30)),
            'completed_at' => now()->subDays(rand(0, 29)),
        ]);
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    public function forWorkout(int $workoutId): static
    {
        return $this->state(fn (array $attributes) => [
            'workout_id' => $workoutId,
        ]);
    }
}
