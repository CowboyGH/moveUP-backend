<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $userSubscription = UserSubscription::inRandomOrder()->first();

        if ($userSubscription) {
            $user = $userSubscription->user;
            $subscription = $userSubscription->subscription;
            $createdAt = $userSubscription->created_at;
        } else {
            $user = User::whereHas('role', function($q) {
                $q->where('name', 'user');
            })->inRandomOrder()->first() ?? User::factory()->user()->create();

            $subscription = Subscription::inRandomOrder()->first()
                ?? Subscription::factory()->create();

            $createdAt = fake()->dateTimeBetween('-1 year', 'now');
        }

        $statuses = ['completed', 'completed', 'completed', 'completed', 'pending', 'pending', 'failed'];
        $status = $this->faker->randomElement($statuses);

        $timestamp = $createdAt instanceof \DateTime
            ? $createdAt->getTimestamp()
            : $createdAt->timestamp;

        $transactionId = match($status) {
            'completed' => 'pay_' . uniqid() . '_' . $timestamp,
            'pending' => 'pending_' . uniqid() . '_' . $timestamp,
            'failed' => 'failed_' . uniqid() . '_' . $timestamp,
            default => 'tx_' . uniqid() . '_' . $timestamp,
        };

        return [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'saved_card_id' => $user->savedCards()->inRandomOrder()->first()?->id,
            'transaction_id' => $transactionId,
            'amount' => $subscription->price,
            'status' => $status,
            'payment_data' => json_encode([
                'payment_method' => 'card',
                'subscription_name' => $subscription->name,
                'subscription_duration' => $subscription->duration_days,
                'processed_at' => $createdAt->format('Y-m-d H:i:s'),
                'status' => $status,
            ]),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function forUserSubscription(UserSubscription $userSubscription): static
    {
        return $this->state(function (array $attributes) use ($userSubscription) {
            $timestamp = $userSubscription->created_at instanceof \DateTime
                ? $userSubscription->created_at->getTimestamp()
                : $userSubscription->created_at->timestamp;

            return [
                'user_id' => $userSubscription->user_id,
                'subscription_id' => $userSubscription->subscription_id,
                'amount' => $userSubscription->subscription->price,
                'created_at' => $userSubscription->created_at,
                'updated_at' => $userSubscription->created_at,
                'status' => 'completed',
                'transaction_id' => 'pay_' . uniqid() . '_' . $timestamp,
            ];
        });
    }

    public function forMonth(int $monthsAgo): static
    {
        return $this->state(function (array $attributes) use ($monthsAgo) {
            $createdAt = now()->subMonths($monthsAgo)->day($this->faker->numberBetween(1, 25));
            $timestamp = $createdAt->timestamp;

            return [
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'transaction_id' => 'pay_' . uniqid() . '_' . $timestamp,
            ];
        });
    }

    public function withStatus(string $status): static
    {
        return $this->state(function (array $attributes) use ($status) {
            $createdAt = $attributes['created_at'] ?? now();
            $timestamp = $createdAt instanceof \DateTime
                ? $createdAt->getTimestamp()
                : $createdAt->timestamp;

            $transactionId = match($status) {
                'completed' => 'pay_' . uniqid() . '_' . $timestamp,
                'pending' => 'pending_' . uniqid() . '_' . $timestamp,
                'failed' => 'failed_' . uniqid() . '_' . $timestamp,
                default => 'tx_' . uniqid() . '_' . $timestamp,
            };

            return [
                'status' => $status,
                'transaction_id' => $transactionId,
            ];
        });
    }

    public function completed(): static
    {
        return $this->withStatus('completed');
    }

    public function pending(): static
    {
        return $this->withStatus('pending');
    }

    public function failed(): static
    {
        return $this->withStatus('failed');
    }

    public function withSubscriptionType(string $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $subscription = Subscription::where('name', $type)->first()
                ?? Subscription::factory()->{$this->getSubscriptionFactoryMethod($type)}()->create();

            return [
                'subscription_id' => $subscription->id,
                'amount' => $subscription->price,
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
}
