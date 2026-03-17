<?php
// database/seeders/UserSubscriptionSeeder.php

namespace Database\Seeders;

use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 50): void
    {
        UserSubscription::factory()
            ->count(intval($count * 0.3))
            ->create();

        UserSubscription::factory()
            ->count(intval($count * 0.2))
            ->forMonth(1)
            ->create();

        UserSubscription::factory()
            ->count(intval($count * 0.15))
            ->forMonth(2)
            ->create();

        UserSubscription::factory()
            ->count(intval($count * 0.1))
            ->forMonth(3)
            ->create();

        UserSubscription::factory()
            ->count(intval($count * 0.15))
            ->forMonth(fake()->numberBetween(4, 6))
            ->create();

        UserSubscription::factory()
            ->count(intval($count * 0.1))
            ->forMonth(fake()->numberBetween(7, 12))
            ->create();

        $types = ['1 месяц', '3 месяца', '6 месяцев', '12 месяцев'];
        $typeCount = intval($count * 0.2);

        foreach ($types as $type) {
            UserSubscription::factory()
                ->count($typeCount)
                ->withSubscriptionType($type)
                ->create();
        }
    }
}
