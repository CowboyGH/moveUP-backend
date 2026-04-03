<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    private array $workoutsByPhase = [
        1 => [
            ['title' => 'Вводная тренировка', 'type' => 'general', 'duration' => 30, 'image' => 'workouts/intro-workout.jpg'],
            ['title' => 'Техника приседаний', 'type' => 'general', 'duration' => 35, 'image' => 'workouts/squat-technique.jpg'],
            ['title' => 'Техника жимов', 'type' => 'general', 'duration' => 35, 'image' => 'workouts/press-technique.jpg'],
            ['title' => 'Техника становой тяги', 'type' => 'general', 'duration' => 35, 'image' => 'workouts/deadlift-technique.jpg'],
            ['title' => 'Кардио-адаптация', 'type' => 'cardio', 'duration' => 25, 'image' => 'workouts/cardio-adaptation.jpg'],
        ],
        2 => [
            ['title' => 'Силовая: Грудь + трицепс', 'type' => 'strength', 'duration' => 45, 'image' => 'workouts/strength-chest-triceps.jpg'],
            ['title' => 'Силовая: Спина + бицепс', 'type' => 'strength', 'duration' => 45, 'image' => 'workouts/strength-back-biceps.jpg'],
            ['title' => 'Силовая: Ноги + плечи', 'type' => 'strength', 'duration' => 50, 'image' => 'workouts/strength-legs-shoulders.jpg'],
            ['title' => 'Силовая: База 5x5', 'type' => 'strength', 'duration' => 40, 'image' => 'workouts/strength-5x5.jpg'],
        ],
        3 => [
            ['title' => 'Объемная: Грудь', 'type' => 'hypertrophy', 'duration' => 50, 'image' => 'workouts/hypertrophy-chest.jpg'],
            ['title' => 'Объемная: Спина', 'type' => 'hypertrophy', 'duration' => 50, 'image' => 'workouts/hypertrophy-back.jpg'],
            ['title' => 'Объемная: Ноги', 'type' => 'hypertrophy', 'duration' => 55, 'image' => 'workouts/hypertrophy-legs.jpg'],
            ['title' => 'Объемная: Плечи + руки', 'type' => 'hypertrophy', 'duration' => 45, 'image' => 'workouts/hypertrophy-shoulders-arms.jpg'],
            ['title' => 'Объемная: Фулл-боди', 'type' => 'hypertrophy', 'duration' => 50, 'image' => 'workouts/hypertrophy-fullbody.jpg'],
        ],
        4 => [
            ['title' => 'HIIT: Спринты', 'type' => 'hiit', 'duration' => 25, 'image' => 'workouts/hiit-sprints.jpg'],
            ['title' => 'Круговая жиросжигающая', 'type' => 'circuit', 'duration' => 35, 'image' => 'workouts/circuit-fat-burn.jpg'],
            ['title' => 'Кардио-силовая', 'type' => 'hiit', 'duration' => 30, 'image' => 'workouts/cardio-strength.jpg'],
            ['title' => 'Табата-тренировка', 'type' => 'hiit', 'duration' => 20, 'image' => 'workouts/tabata.jpg'],
        ],
        5 => [
            ['title' => 'Активное восстановление', 'type' => 'functional', 'duration' => 30, 'image' => 'workouts/active-recovery.jpg'],
            ['title' => 'Растяжка', 'type' => 'functional', 'duration' => 40, 'image' => 'workouts/stretching.jpg'],
            ['title' => 'Йога', 'type' => 'functional', 'duration' => 45, 'image' => 'workouts/yoga.jpg'],
        ],
    ];

    public function run(): void
    {
        $phases = Phase::all();
        $createdCount = 0;

        foreach ($phases as $phase) {
            if (!isset($this->workoutsByPhase[$phase->order_number])) {
                continue;
            }

            foreach ($this->workoutsByPhase[$phase->order_number] as $workoutData) {
                Workout::updateOrCreate(
                    ['title' => $workoutData['title']],
                    [
                        'phase_id' => $phase->id,
                        'type' => $workoutData['type'],
                        'description' => $this->getDescription($workoutData['title']),
                        'duration_minutes' => $workoutData['duration'],
                        'image' => $workoutData['image'] ?? 'workouts/default.jpg',
                        'is_active' => true,
                    ]
                );
                $createdCount++;
            }
        }

        $factoryCount = 5;
        Workout::factory($factoryCount)->create();

        $totalCount = Workout::count();
        $this->command->info("Создано {$createdCount} тренировок из списка + {$factoryCount} через фабрику. Всего: {$totalCount}");
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
