<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    private array $workoutsByPhase = [
        1 => [
            ['title' => 'Вводная тренировка', 'type' => 'general', 'duration' => 30,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Техника приседаний', 'type' => 'general', 'duration' => 35,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Техника жимов', 'type' => 'general', 'duration' => 35,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Техника становой тяги', 'type' => 'general', 'duration' => 35,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Кардио-адаптация', 'type' => 'cardio', 'duration' => 25],
        ],
        2 => [
            ['title' => 'Силовая: Грудь + трицепс', 'type' => 'strength', 'duration' => 45,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Силовая: Спина + бицепс', 'type' => 'strength', 'duration' => 45,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Силовая: Ноги + плечи', 'type' => 'strength', 'duration' => 50,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Силовая: База 5x5', 'type' => 'strength', 'duration' => 40,'image' => 'workouts/intro-workout.jpg'],
        ],
        3 => [
            ['title' => 'Объемная: Грудь', 'type' => 'hypertrophy', 'duration' => 50,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Объемная: Спина', 'type' => 'hypertrophy', 'duration' => 50,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Объемная: Ноги', 'type' => 'hypertrophy', 'duration' => 55,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Объемная: Плечи + руки', 'type' => 'hypertrophy', 'duration' => 45,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Объемная: Фулл-боди', 'type' => 'hypertrophy', 'duration' => 50,'image' => 'workouts/intro-workout.jpg'],
        ],
        4 => [
            ['title' => 'HIIT: Спринты', 'type' => 'hiit', 'duration' => 25,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Круговая жиросжигающая', 'type' => 'circuit', 'duration' => 35,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Кардио-силовая', 'type' => 'hiit', 'duration' => 30,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Табата-тренировка', 'type' => 'hiit', 'duration' => 20,'image' => 'workouts/intro-workout.jpg'],
        ],
        5 => [
            ['title' => 'Активное восстановление', 'type' => 'functional', 'duration' => 30,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Растяжка', 'type' => 'functional', 'duration' => 40,'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Йога', 'type' => 'functional', 'duration' => 45,'image' => 'workouts/intro-workout.jpg'],
        ],
    ];

    public function run(): void
    {
        $phases = Phase::all();

        foreach ($phases as $phase) {
            if (!isset($this->workoutsByPhase[$phase->order_number])) {
                continue;
            }

            foreach ($this->workoutsByPhase[$phase->order_number] as $workoutData) {
                Workout::firstOrCreate(
                    ['title' => $workoutData['title']],
                    [
                        'phase_id' => $phase->id,
                        'type' => $workoutData['type'],
                        'description' => $this->getDescription($workoutData['title']),
                        'duration_minutes' => $workoutData['duration'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function getDescription(string $title): string
    {
        $descriptions = [
            'Вводная тренировка' => 'Первая тренировка для знакомства с основными движениями. Выполняем упражнения с минимальным весом, отрабатываем технику.',
            'Техника приседаний' => 'Детальный разбор техники приседаний: положение штанги, глубина, дыхание. Работаем с пустым грифом или легкими весами.',
            'Техника жимов' => 'Изучение техники жимов лежа и стоя. Положение локтей, траектория движения, дыхание.',
            'Техника становой тяги' => 'Освоение правильной техники становой тяги: настрой, хват, положение спины, подъем и опускание штанги.',
            'Кардио-адаптация' => 'Легкая кардио-тренировка для подготовки сердечно-сосудистой системы к нагрузкам.',
            'Силовая: База 5x5' => 'Классическая силовая программа 5x5: 5 подходов по 5 повторений в базовых упражнениях.',
            'Активное восстановление' => 'Легкие упражнения для улучшения кровообращения и ускорения восстановления мышц.',
            'Растяжка' => 'Комплекс упражнений на растяжку всех мышечных групп для улучшения гибкости и восстановления.',
            'Йога' => 'Упражнения из йоги для развития гибкости, баланса и ментального расслабления.',
        ];

        return $descriptions[$title] ?? 'Описание тренировки будет добавлено позже.';
    }
}
