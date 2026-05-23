<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        Level::factory()->beginner()->create();
        Level::factory()->intermediate()->create();
        Level::factory()->advanced()->create();
    }
}
