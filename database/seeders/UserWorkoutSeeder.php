<?php

namespace Database\Seeders;

use App\Models\UserWorkout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserWorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserWorkout::factory(50)->create();
    }
}
