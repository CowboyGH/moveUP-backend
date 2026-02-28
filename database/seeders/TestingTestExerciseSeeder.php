<?php

namespace Database\Seeders;

use App\Models\TestingTestExercise;
use Illuminate\Database\Seeder;

class TestingTestExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestingTestExercise::factory(20)->create();
    }
}
