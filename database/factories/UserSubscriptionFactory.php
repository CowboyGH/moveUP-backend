<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $user = User::whereHas('role', function($q) {
            $q->where('name', 'user');
        })->inRandomOrder()->first() ?? User::factory()->user()->create();

        $subscription = Subscription::inRandomOrder()->first()
            ?? Subscription::factory()->create();

        $monthOffset = $this->faker->numberBetween(0, 11);
        $probability = [40, 30, 15, 10, 5, 5, 5, 5, 5, 5, 5, 5];

        $weightedOffset = $this->getWeightedMonthOffset($probability);

        $startDate = now()->subMonths($weightedOffset)->subDays($this->faker->numberBetween(0, 25));

        $startDate = $startDate->day($this->faker->numberBetween(1, 25));
        $endDate = (clone $startDate)->addDays($subscription->duration_days);

        return [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => now()->between($startDate, $endDate),
            'created_at' => $startDate,
            'updated_at' => $startDate,
        ];
    }

    private function getWeightedMonthOffset(array $probabilities): int
    {
        $rand = $this->faker->numberBetween(1, 100);
        $cumulative = 0;

        foreach ($probabilities as $offset => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $offset;
            }
        }

        return 0;
    }

    public function withSubscriptionType(string $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $subscription = Subscription::where('name', $type)->first()
                ?? Subscription::factory()->{$this->getSubscriptionFactoryMethod($type)}()->create();

            return [
                'subscription_id' => $subscription->id,
            ];
        });
    }

    public function forMonth(int $monthsAgo): static
    {
        return $this->state(function (array $attributes) use ($monthsAgo) {
            $startDate = now()->subMonths($monthsAgo)->day($this->faker->numberBetween(1, 25));
            $subscription = Subscription::find($attributes['subscription_id'] ?? null)
                ?? Subscription::inRandomOrder()->first();

            if (!$subscription) {
                $subscription = Subscription::factory()->create();
            }

            $endDate = (clone $startDate)->addDays($subscription->duration_days);

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => now()->between($startDate, $endDate),
                'created_at' => $startDate,
                'updated_at' => $startDate,
            ];
        });
    }

    private function getSubscriptionFactoryMethod(string $type): string
    {
        return match($type) {
            '1 месяц' => 'basicOneMonth',
            '3 месяца' => 'proThreeMonths',
            '6 месяцев' => 'premiumSixMonths',
            '12 месяцев' => 'ultimateYearly',
            default => 'basicOneMonth',
        };
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $subscription = Subscription::find($attributes['subscription_id'] ?? null)
                ?? Subscription::inRandomOrder()->first()
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
            $subscription = Subscription::find($attributes['subscription_id'] ?? null)
                ?? Subscription::inRandomOrder()->first()
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
        return $this->withSubscriptionType('1 месяц');
    }

    public function withThreeMonths(): static
    {
        return $this->withSubscriptionType('3 месяца');
    }

    public function withSixMonths(): static
    {
        return $this->withSubscriptionType('6 месяцев');
    }

    public function withYearly(): static
    {
        return $this->withSubscriptionType('12 месяцев');
    }

    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-7 days', 'now');
            $subscription = Subscription::find($attributes['subscription_id'] ?? null)
                ?? Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $endDate = (clone $startDate)->modify("+{$subscription->duration_days} days");

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => now()->between($startDate, $endDate),
                'created_at' => $startDate,
                'updated_at' => $startDate,
            ];
        });
    }
}
