<?php

namespace Database\Seeders;

use App\Models\UserWarmupPerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserWarmupPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserWarmupPerformance::factory(20)->create();
    }
}
