<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $durationOptions = [30, 90, 180, 365];
        return [
            'name' => fake()->randomElement(['1 month', '3 month', '6 month', '12 month']),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 0, 99.99),
            'duration_days' => fake()->randomElement($durationOptions),
            'is_active' => fake()->boolean(85),
        ];
    }

    public function basicOneMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '1 month',
            'price' => 9.99,
            'duration_days' => 30,
            'is_active' => true,
        ]);
    }

    public function proThreeMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '3 month',
            'price' => 19.99,
            'duration_days' => 90,
            'is_active' => true,
        ]);
    }

    public function premiumSixMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '6 month',
            'price' => 29.99,
            'duration_days' => 180,
            'is_active' => true,
        ]);
    }

    public function ultimateYearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '12 month',
            'price' => 49.99,
            'duration_days' => 365,
            'is_active' => true,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
