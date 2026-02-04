<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $muscleGroups = [
            'Грудь', 'Спина', 'Плечи', 'Бицепс', 'Трицепс',
            'Ноги', 'Ягодицы', 'Пресс', 'Кардио', 'Все тело'
        ];

        return [
            'equipment_id' => Equipment::inRandomOrder()->first()->id
                ?? Equipment::factory()->create()->id,
            'title' => fake()->words(3, true), // Убрал unique() чтобы избежать ошибок
            'description' => fake()->paragraph(3),
            'image' => 'exercises/exercise-' . fake()->numberBetween(1, 20) . '.jpg',
            'muscle_group' => fake()->randomElement($muscleGroups),
        ];
    }

    // Состояния для разных типов оборудования
    public function forGym(): static
    {
        return $this->state(function (array $attributes) {
            $gymEquipment = Equipment::where('name', 'Зал')->first();

            return [
                'equipment_id' => $gymEquipment ? $gymEquipment->id : Equipment::factory()->gym()->create()->id,
                'title' => fake()->words(2, true) . ' для зала',
            ];
        });
    }

    public function forMixed(): static
    {
        return $this->state(function (array $attributes) {
            $mixedEquipment = Equipment::where('name', 'Смешанное')->first();

            return [
                'equipment_id' => $mixedEquipment ? $mixedEquipment->id : Equipment::factory()->mixed()->create()->id,
                'title' => fake()->words(2, true) . ' универсальное',
            ];
        });
    }

    // Состояния для мышечных групп
    public function chest(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Грудь',
            'title' => fake()->words(2, true) . ' для груди',
        ]);
    }

    public function back(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Спина',
            'title' => fake()->words(2, true) . ' для спины',
        ]);
    }

    public function shoulders(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Плечи',
            'title' => fake()->words(2, true) . ' для плеч',
        ]);
    }

    public function legs(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Ноги',
            'title' => fake()->words(2, true) . ' для ног',
        ]);
    }

    public function glutes(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Ягодицы',
            'title' => fake()->words(2, true) . ' для ягодиц',
        ]);
    }

    public function abs(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Пресс',
            'title' => fake()->words(2, true) . ' для пресса',
        ]);
    }

    public function cardio(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Кардио',
            'title' => fake()->words(2, true) . ' кардио упражнение',
        ]);
    }

    public function fullBody(): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => 'Все тело',
            'title' => fake()->words(2, true) . ' комплексное упражнение',
        ]);
    }

    // Состояния для разных типов упражнений
    public function strength(): static
    {
        $strengthExercises = [
            'title' => fake()->randomElement([
                    'Жим штанги', 'Тяга блока', 'Приседания', 'Становая тяга',
                    'Жим гантелей', 'Подтягивания', 'Отжимания', 'Выпады'
                ]) . ' ' . fake()->word(),
            'description' => 'Силовое упражнение для развития мышц и увеличения силы.',
        ];

        return $this->state(fn (array $attributes) => $strengthExercises);
    }

    public function endurance(): static
    {
        $enduranceExercises = [
            'title' => fake()->randomElement([
                    'Берпи', 'Скакалка', 'Бег на месте', 'Велотренажер',
                    'Прыжки', 'Альпинист', 'Бокс', 'Планка'
                ]) . ' ' . fake()->word(),
            'description' => 'Упражнение на выносливость и кардио-систему.',
        ];

        return $this->state(fn (array $attributes) => $enduranceExercises);
    }

    public function stretching(): static
    {
        $stretchingExercises = [
            'title' => fake()->randomElement([
                'Растяжка ног', 'Наклоны', 'Мостик', 'Бабочка',
                'Скручивания', 'Наклон к ногам', 'Растяжка спины'
            ]),
            'description' => 'Упражнения на растяжку и гибкость.',
        ];

        return $this->state(fn (array $attributes) => $stretchingExercises);
    }
}
