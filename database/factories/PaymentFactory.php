<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SavedCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        static $userPaymentCounts = [];

        $user = User::whereHas('role', function($q) {
            $q->where('name', 'user');
        })->inRandomOrder()->first() ?? User::factory()->user()->create();

        $userPaymentCounts[$user->id] = ($userPaymentCounts[$user->id] ?? 0) + 1;

        $statuses = ['completed', 'completed', 'pending', 'failed'];
        $status = $this->faker->randomElement($statuses);

        return [
            'user_id' => $user->id,
            'subscription_id' => Subscription::inRandomOrder()->first()->id
                ?? Subscription::factory(),
            'saved_card_id' => $user->savedCards()->inRandomOrder()->first()?->id,
            'transaction_id' => $this->faker->unique()->uuid(),
            'amount' => $this->faker->randomFloat(2, 5, 100),
            'status' => $status,
            'payment_data' => $status === 'completed' ? json_encode([
                'payment_method' => 'card',
                'processed_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            ]) : null,
        ];
    }
}
