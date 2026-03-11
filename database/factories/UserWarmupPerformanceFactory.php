<?php

namespace Database\Factories;

use App\Models\Warmup;
use App\Models\UserWorkout;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserWarmupPerformanceFactory extends Factory
{
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

    public function forWarmup(Warmup $warmup): self
    {
        return $this->state(['warmup_id' => $warmup->id]);
    }

    public function forUserWorkout(UserWorkout $userWorkout): self
    {
        return $this->state(['user_workout_id' => $userWorkout->id]);
    }
}
