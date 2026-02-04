<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Goal::factory()->strengthGrowth()->create();
        Goal::factory()->muscleMass()->create();
        Goal::factory()->fatLoss()->create();
        Goal::factory()->generalHealth()->create();
    }
}
