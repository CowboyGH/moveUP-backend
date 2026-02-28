<?php

namespace Database\Seeders;

use App\Models\SavedCard;
use Illuminate\Database\Seeder;

class SavedCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SavedCard::factory(15)->create();
    }
}
