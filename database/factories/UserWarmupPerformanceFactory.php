<?php

namespace Database\Factories;

use App\Models\Warmup;
use App\Models\UserWorkout;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserWarmupPerformance>
 */
class UserWarmupPerformanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warmup_id' => Warmup::factory(),
            'user_workout_id' => UserWorkout::factory(),
            'completed' => $this->faker->boolean(70),
        ];
    }

    public function completed(): self
    {
        return $this->state(['completed' => true]);
    }

    public function notCompleted(): self
    {
        return $this->state(['completed' => false]);
    }

    /**
     * Связываем с конкретной разминкой
     */
    public function forWarmup(Warmup $warmup): self
    {
        return $this->state(['warmup_id' => $warmup->id]);
    }

    /**
     * Связываем с конкретной тренировкой пользователя
     */
    public function forUserWorkout(UserWorkout $userWorkout): self
    {
        return $this->state(['user_workout_id' => $userWorkout->id]);
    }
}
