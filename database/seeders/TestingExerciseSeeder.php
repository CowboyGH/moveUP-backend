<?php

namespace Database\Seeders;

use App\Models\TestingExercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestingExercise::factory(20)->create();
    }
}
