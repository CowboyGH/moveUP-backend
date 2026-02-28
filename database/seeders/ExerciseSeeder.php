<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exercise::factory(3)->forGym()->create();
        Exercise::factory(3)->forMixed()->create();

        Exercise::factory()->chest()->create();
        Exercise::factory()->back()->create();
        Exercise::factory()->shoulders()->create();
        Exercise::factory()->legs()->create();
        Exercise::factory()->glutes()->create();
        Exercise::factory()->abs()->create();
        Exercise::factory()->cardio()->create();
        Exercise::factory()->fullBody()->create();

        Exercise::factory()->forGym()->strength()->create();
        Exercise::factory()->forMixed()->endurance()->create();
        Exercise::factory()->stretching()->create();
    }
}
