<?php

namespace Database\Seeders;

use App\Models\UserExerciseWeight;
use Illuminate\Database\Seeder;

class UserExerciseWeightSeeder extends Seeder
{
    public function run(): void
    {
        UserExerciseWeight::factory()->count(20)->create();
    }
}
