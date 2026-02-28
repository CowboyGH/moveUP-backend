<?php

namespace Database\Seeders;

use App\Models\WorkoutExercise;
use Illuminate\Database\Seeder;

class WorkoutExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkoutExercise::factory(40)->create();
    }
}
