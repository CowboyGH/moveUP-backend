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
        $status = $this->faker->randomElement([
            UserWorkout::STATUS_ASSIGNED,
            UserWorkout::STATUS_STARTED,
            UserWorkout::STATUS_COMPLETED,
        ], [
            UserWorkout::STATUS_ASSIGNED => 40,
            UserWorkout::STATUS_STARTED => 20,
            UserWorkout::STATUS_COMPLETED => 40,
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
            'user_id' => User::factory(),
            'workout_id' => Workout::factory(),
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'status' => $status,
        ];
    }
    public function assigned(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserWorkout::STATUS_ASSIGNED,
                'started_at' => null,
                'completed_at' => null,
            ];
        });
    }
    public function started(): static
    {
        return $this->state(function (array $attributes) {
            $startedAt = now()->subHours(rand(1, 48));

            return [
                'status' => UserWorkout::STATUS_STARTED,
                'started_at' => $startedAt,
                'completed_at' => null,
            ];
        });
    }
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startedAt = now()->subDays(rand(1, 30));
            $completedAt = (clone $startedAt)->addHours(rand(1, 3));

            return [
                'status' => UserWorkout::STATUS_COMPLETED,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
            ];
        });
    }
    public function forUser(int $userId): static
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }
    public function forWorkout(int $workoutId): static
    {
        return $this->state(function (array $attributes) use ($workoutId) {
            return [
                'workout_id' => $workoutId,
            ];
        });
    }
}
