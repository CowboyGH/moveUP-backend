<?php

namespace Database\Seeders;

use App\Models\SavedCard;
use Illuminate\Database\Seeder;

class SavedCardSeeder extends Seeder
{
    public function run(): void
    {
        SavedCard::factory(15)->create();
    }
}
