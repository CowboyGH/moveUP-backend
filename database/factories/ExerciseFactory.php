<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExerciseFactory extends Factory
{
    private array $realExercises = [
        ['title' => 'Жим штанги лежа', 'muscle_group' => 'Грудь', 'type' => 'strength'],
        ['title' => 'Жим гантелей на наклонной скамье', 'muscle_group' => 'Грудь', 'type' => 'strength'],
        ['title' => 'Сведение рук в кроссовере', 'muscle_group' => 'Грудь', 'type' => 'isolation'],
        ['title' => 'Отжимания на брусьях', 'muscle_group' => 'Грудь', 'type' => 'strength'],

        ['title' => 'Тяга верхнего блока к груди', 'muscle_group' => 'Спина', 'type' => 'strength'],
        ['title' => 'Тяга гантели в наклоне', 'muscle_group' => 'Спина', 'type' => 'strength'],
        ['title' => 'Подтягивания широким хватом', 'muscle_group' => 'Спина', 'type' => 'strength'],
        ['title' => 'Становая тяга', 'muscle_group' => 'Спина', 'type' => 'strength'],

        ['title' => 'Приседания со штангой', 'muscle_group' => 'Ноги', 'type' => 'strength'],
        ['title' => 'Румынская тяга', 'muscle_group' => 'Ноги', 'type' => 'strength'],
        ['title' => 'Выпады с гантелями', 'muscle_group' => 'Ноги', 'type' => 'strength'],
        ['title' => 'Жим ногами в тренажере', 'muscle_group' => 'Ноги', 'type' => 'strength'],

        ['title' => 'Жим гантелей сидя', 'muscle_group' => 'Плечи', 'type' => 'strength'],
        ['title' => 'Махи гантелями в стороны', 'muscle_group' => 'Плечи', 'type' => 'isolation'],
        ['title' => 'Тяга штанги к подбородку', 'muscle_group' => 'Плечи', 'type' => 'strength'],

        ['title' => 'Отжимания от пола', 'muscle_group' => 'Грудь', 'type' => 'strength'],
        ['title' => 'Тяга нижнего блока', 'muscle_group' => 'Спина', 'type' => 'strength'],
        ['title' => 'Приседания с гантелями', 'muscle_group' => 'Ноги', 'type' => 'strength'],
        ['title' => 'Подъем на носки стоя', 'muscle_group' => 'Ноги', 'type' => 'isolation'],
        ['title' => 'Разведение гантелей лежа', 'muscle_group' => 'Грудь', 'type' => 'isolation'],
    ];

    private array $descriptions = [
        'Грудь' => 'Базовое упражнение для развития грудных мышц. Выполняется лежа на скамье.',
        'Спина' => 'Упражнение для развития широчайших мышц спины.',
        'Ноги' => 'Базовое упражнение для развития мышц ног и ягодиц.',
        'Плечи' => 'Упражнение для развития дельтовидных мышц.',
        'Пресс' => 'Упражнение для развития мышц брюшного пресса.',
        'Ягодицы' => 'Упражнение для развития ягодичных мышц.',
        'Кардио' => 'Кардио-упражнение для развития выносливости и сжигания калорий.',
    ];

    public function definition(): array
    {
        $exercise = $this->faker->randomElement($this->realExercises);

        return [
            'equipment_id' => $this->getEquipmentForExercise($exercise['title']),
            'title' => $exercise['title'],
            'description' => $this->descriptions[$exercise['muscle_group']] ?? 'Описание упражнения',
            'image' => 'exercises/' . $this->getImageName($exercise['title']),
            'muscle_group' => $exercise['muscle_group'],
        ];
    }

    private function getEquipmentForExercise(string $title): int
    {
        $gymExercises = ['Жим штанги', 'Тяга верхнего блока', 'Приседания со штангой', 'Становая тяга', 'Жим ногами'];
        $isGym = collect($gymExercises)->contains(fn($item) => str_contains($title, $item));

        $equipment = Equipment::where('name', $isGym ? 'Зал' : 'Смешанное')->first();

        if (!$equipment) {
            $equipment = Equipment::factory()->create(['name' => $isGym ? 'Зал' : 'Смешанное']);
        }

        return $equipment->id;
    }

    private function getImageName(string $title): string
    {
        $slug = Str::slug($title);
        return "exercise-{$slug}.jpg";
    }
}
