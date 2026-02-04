<?php

namespace Database\Seeders;

use App\Models\ExercisePerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExercisePerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExercisePerformance::factory()->count(50)->create();
    }
}
