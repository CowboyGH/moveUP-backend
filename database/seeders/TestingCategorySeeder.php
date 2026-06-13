<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Testing;
use Illuminate\Database\Seeder;
use RuntimeException;

class TestingCategorySeeder extends Seeder
{
    private array $map = [
        'Тест Купера (12-минутный бег)' => ['Кардио', 'Выносливость'],
        'Гарвардский степ-тест' => ['Кардио', 'Выносливость'],
        'Тест Руфье' => ['Кардио'],
        'Определение максимальной силы (1ПМ)' => ['Сила'],
        'Гибкость: Тест "Сядь и достань"' => ['Гибкость'],
        'Тест на выносливость мышц кора' => ['Сила', 'Выносливость'],
        'Взрывная сила: Прыжок в длину с места' => ['Сила'],
        'Скоростно-силовая выносливость' => ['Выносливость', 'Сила'],
        'Координация и ловкость: Челночный бег' => ['Координация'],
        'Баланс и стабильность' => ['Баланс', 'Координация'],
    ];

    public function run(): void
    {
        $total = 0;

        foreach ($this->map as $title => $names) {
            $testing = Testing::where('title', $title)->first();

            if (! $testing) {
                throw new RuntimeException(
                    "TestingCategorySeeder: тест '{$title}' не найден. "
                    . "Проверь TestingSeeder или обнови \$map."
                );
            }

            $found = Category::whereIn('name', $names)->pluck('name')->all();
            $missing = array_diff($names, $found);

            if (! empty($missing)) {
                throw new RuntimeException(
                    "TestingCategorySeeder: категории " . implode(', ', $missing)
                    . " не найдены для теста '{$title}'. Проверь CategorySeeder или обнови \$map."
                );
            }

            $ids = Category::whereIn('name', $names)->pluck('id')->all();

            $testing->categories()->syncWithoutDetaching($ids);

            $total += count($ids);
        }

        $this->command->info('Привязок тест-категория: ' . $total);
    }
}
