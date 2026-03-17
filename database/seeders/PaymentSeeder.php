<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $userSubscriptions = UserSubscription::all();

        foreach ($userSubscriptions as $userSubscription) {
            Payment::factory()
                ->forUserSubscription($userSubscription)
                ->completed()
                ->create();
        }
    }
}
