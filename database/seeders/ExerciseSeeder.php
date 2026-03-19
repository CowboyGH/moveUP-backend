<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseSeeder extends Seeder
{
    private array $exercises = [
        ['title' => 'Жим штанги лежа', 'muscle_group' => 'Грудь', 'equipment' => 'Зал'],
        ['title' => 'Жим гантелей на наклонной скамье', 'muscle_group' => 'Грудь', 'equipment' => 'Зал'],
        ['title' => 'Сведение рук в кроссовере', 'muscle_group' => 'Грудь', 'equipment' => 'Зал'],
        ['title' => 'Отжимания от пола', 'muscle_group' => 'Грудь', 'equipment' => 'Смешанное'],

        ['title' => 'Тяга верхнего блока', 'muscle_group' => 'Спина', 'equipment' => 'Зал'],
        ['title' => 'Подтягивания', 'muscle_group' => 'Спина', 'equipment' => 'Зал'],
        ['title' => 'Тяга гантели в наклоне', 'muscle_group' => 'Спина', 'equipment' => 'Зал'],
        ['title' => 'Гиперэкстензия', 'muscle_group' => 'Спина', 'equipment' => 'Зал'],

        ['title' => 'Приседания со штангой', 'muscle_group' => 'Ноги', 'equipment' => 'Зал'],
        ['title' => 'Румынская тяга', 'muscle_group' => 'Ноги', 'equipment' => 'Зал'],
        ['title' => 'Выпады с гантелями', 'muscle_group' => 'Ноги', 'equipment' => 'Смешанное'],
        ['title' => 'Жим ногами', 'muscle_group' => 'Ноги', 'equipment' => 'Зал'],
        ['title' => 'Приседания без веса', 'muscle_group' => 'Ноги', 'equipment' => 'Смешанное'],

        ['title' => 'Жим гантелей сидя', 'muscle_group' => 'Плечи', 'equipment' => 'Зал'],
        ['title' => 'Махи гантелями в стороны', 'muscle_group' => 'Плечи', 'equipment' => 'Зал'],
        ['title' => 'Армейский жим', 'muscle_group' => 'Плечи', 'equipment' => 'Зал'],

        ['title' => 'Скручивания на пресс', 'muscle_group' => 'Пресс', 'equipment' => 'Смешанное'],
        ['title' => 'Подъем ног в висе', 'muscle_group' => 'Пресс', 'equipment' => 'Зал'],
        ['title' => 'Планка', 'muscle_group' => 'Пресс', 'equipment' => 'Смешанное'],
        ['title' => 'Русский твист', 'muscle_group' => 'Пресс', 'equipment' => 'Смешанное'],

        ['title' => 'Ягодичный мостик', 'muscle_group' => 'Ягодицы', 'equipment' => 'Смешанное'],
        ['title' => 'Отведение ноги в кроссовере', 'muscle_group' => 'Ягодицы', 'equipment' => 'Зал'],
        ['title' => 'Приседания плие', 'muscle_group' => 'Ягодицы', 'equipment' => 'Смешанное'],

        ['title' => 'Бег на дорожке', 'muscle_group' => 'Кардио', 'equipment' => 'Зал'],
        ['title' => 'Скакалка', 'muscle_group' => 'Кардио', 'equipment' => 'Смешанное'],
        ['title' => 'Берпи', 'muscle_group' => 'Кардио', 'equipment' => 'Смешанное'],
        ['title' => 'Велотренажер', 'muscle_group' => 'Кардио', 'equipment' => 'Зал'],
    ];

    public function run(): void
    {
        foreach ($this->exercises as $exerciseData) {
            $equipment = Equipment::where('name', $exerciseData['equipment'])->first();

            if (!$equipment) {
                $this->command->error("Оборудование '{$exerciseData['equipment']}' не найдено. Пропускаем упражнение '{$exerciseData['title']}'");
                continue;
            }

            Exercise::firstOrCreate(
                ['title' => $exerciseData['title']],
                [
                    'equipment_id' => $equipment->id,
                    'muscle_group' => $exerciseData['muscle_group'],
                    'description' => $this->getDescription($exerciseData['title']),
                    'image' => 'exercises/' . $this->getImageName($exerciseData['title']),
                ]
            );

            $this->command->info("Создано упражнение: {$exerciseData['title']}");
        }
    }

    private function getDescription(string $title): string
    {
        $descriptions = [
            'Жим штанги лежа' => 'Базовое упражнение для развития грудных мышц, передних дельт и трицепса. Выполняется лежа на горизонтальной скамье. Штанга опускается до касания груди, затем выжимается вверх.',

            'Приседания со штангой' => 'Базовое упражнение для развития мышц ног, ягодиц и кора. Штанга располагается на верхней части спины. Приседания выполняются до параллели бедер с полом или ниже.',

            'Тяга верхнего блока' => 'Упражнение для развития широчайших мышц спины. Выполняется сидя в тренажере, гриф тянется к верхней части груди.',

            'Подтягивания' => 'Базовое упражнение для развития широчайших мышц спины и бицепса. Выполняется на перекладине широким или узким хватом.',

            'Жим гантелей сидя' => 'Упражнение для развития дельтовидных мышц. Выполняется сидя на скамье с опорой для спины. Гантели выжимаются вверх из положения у плеч.',

            'Становая тяга' => 'Базовое упражнение для развития мышц спины, ног и ягодиц. Штанга поднимается с пола до полного выпрямления корпуса.',

            'Румынская тяга' => 'Упражнение для развития мышц задней поверхности бедра и ягодиц. Выполняется с прямыми ногами, штанга опускается до середины голени.',

            'Бег на дорожке' => 'Кардио-упражнение для развития выносливости и сжигания калорий. Можно использовать различные режимы: равномерный бег, интервалы, подъем в гору.',
        ];

        return $descriptions[$title] ?? "Упражнение '{$title}' для развития мышц. Подробное описание будет добавлено позже.";
    }

    private function getImageName(string $title): string
    {
        $slug = Str::slug($title);
        return "exercise-{$slug}.jpg";
    }
}
