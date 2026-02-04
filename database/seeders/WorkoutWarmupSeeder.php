<?php

namespace Database\Seeders;

use App\Models\WorkoutWarmup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkoutWarmupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkoutWarmup::factory(10)->create();
    }
}
