<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private array $categories = [
        'Кардио',
        'Выносливость',
        'Сила',
        'Гибкость',
        'Координация',
        'Баланс',
    ];

    public function run(): void
    {
        foreach ($this->categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Категорий: ' . Category::count());
    }
}
