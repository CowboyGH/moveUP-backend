<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exercise::factory(5)->forGym()->create();
        Exercise::factory(5)->forMixed()->create();

        Exercise::factory()->chest()->create();
        Exercise::factory()->legs()->create();
        Exercise::factory()->cardio()->create();

        Exercise::factory()->forGym()->strength()->create();
        Exercise::factory()->forMixed()->endurance()->create();
    }
}
