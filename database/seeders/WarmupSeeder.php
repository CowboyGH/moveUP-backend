<?php

namespace Database\Seeders;

use App\Models\Warmup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarmupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warmup::factory(8)->create();
    }
}
