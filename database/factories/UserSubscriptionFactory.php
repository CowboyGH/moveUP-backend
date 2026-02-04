<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSubscription>
 */
class UserSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subscription = Subscription::inRandomOrder()->first();

        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

        return [
            'user_id' => User::factory(),
            'subscription_id' => $subscription->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => now()->between($startDate, $endDate),
        ];
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $startDate = fake()->dateTimeBetween('-30 days', 'now');
            $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ];
        });
    }

    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $startDate = fake()->dateTimeBetween('-1 year', '-31 days');
            $endDate = fake()->dateTimeBetween($startDate, '-1 day');

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false,
            ];
        });
    }

    public function withOneMonth(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::where('name', '1 month')->first()
                ?? Subscription::factory()->basicOneMonth()->create();

            return [
                'subscription_id' => $subscription->id,
            ];
        });
    }

    public function withThreeMonths(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::where('name', '3 month')->first()
                ?? Subscription::factory()->proThreeMonths()->create();

            return [
                'subscription_id' => $subscription->id,
            ];
        });
    }

    public function withSixMonths(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::where('name', '6 month')->first()
                ?? Subscription::factory()->premiumSixMonths()->create();

            return [
                'subscription_id' => $subscription->id,
            ];
        });
    }

    public function withYearly(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::where('name', '12 month')->first()
                ?? Subscription::factory()->ultimateYearly()->create();

            return [
                'subscription_id' => $subscription->id,
            ];
        });
    }

    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-7 days', 'now');
            $subscription = Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => now()->between($startDate, $endDate),
            ];
        });
    }

    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('+1 day', '+30 days');
            $subscription = Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false,
            ];
        });
    }

    public function longTerm(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::where('name', '12 month')->first()
                ?? Subscription::factory()->ultimateYearly()->create();

            $startDate = fake()->dateTimeBetween('-6 months', 'now');
            $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

            return [
                'subscription_id' => $subscription->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => now()->between($startDate, $endDate),
            ];
        });
    }
}
