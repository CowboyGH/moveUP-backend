<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class SavedCardFactory extends Factory
{
    public function definition(): array
    {
        static $userCardCounts = [];

        $user = User::whereHas('role', function($q) {
            $q->where('name', 'user');
        })->withCount('savedCards')
            ->having('saved_cards_count', '<', 2)
            ->inRandomOrder()
            ->first() ?? User::factory()->user()->create();

        $cardCount = $userCardCounts[$user->id] ?? $user->savedCards()->count();
        $userCardCounts[$user->id] = $cardCount + 1;

        $cardNumber = $this->faker->creditCardNumber();
        $lastFour = substr($cardNumber, -4);

        return [
            'user_id' => $user->id,
            'card_holder' => $this->faker->name(),
            'card_number_hash' => Hash::make($cardNumber),
            'card_last_four' => $lastFour,
            'expiry_month' => $this->faker->numberBetween(1, 12),
            'expiry_year' => $this->faker->numberBetween(now()->year, now()->year + 5),
            'is_default' => $cardCount === 0,
        ];
    }
}
