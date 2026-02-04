<?php

namespace Database\Seeders;

use App\Models\Phase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Phase::factory()->preparation()->create();
        Phase::factory()->basic()->create();
        Phase::factory()->intense()->create();
        Phase::factory()->rest()->create();
    }
}
