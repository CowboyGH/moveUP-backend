<?php

namespace Database\Seeders;

use App\Models\ExerciseReaction;
use Illuminate\Database\Seeder;

class ExerciseReactionSeeder extends Seeder
{
    public function run(): void
    {
        ExerciseReaction::factory()->count(30)->create();
    }
}
