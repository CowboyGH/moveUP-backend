<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::factory()->basicOneMonth()->create();
        Subscription::factory()->proThreeMonths()->create();
        Subscription::factory()->premiumSixMonths()->create();
        Subscription::factory()->ultimateYearly()->create();
    }
}
