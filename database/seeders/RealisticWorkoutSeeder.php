<?php

namespace Database\Seeders;

use App\Models\Workout;
use App\Models\Warmup;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\WorkoutWarmup;
use Illuminate\Database\Seeder;

class RealisticWorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $warmupsData = [
            'Суставная мобилизация' => [
                'description' => 'Плавные вращения для шеи, плечевого пояса, позвоночника, коленей и голеностопа. Подготовка связок к нагрузке.',
                'image' => 'warmups/warm-up.png',
            ],
            'Активация пульса' => [
                'description' => 'Бег на месте, джампинг джеки, вращения руками для повышения пульса.',
                'image' => 'warmups/warm-up.png',
            ],
            'Разогрев нижней части' => [
                'description' => 'Махи ногами, приседания без веса, выпады в сторону для подготовки суставов. Движения должны быть амплитудными, чтобы почувствовать тепло в мышцах.',
                'image' => 'warmups/warm-up.png',
            ],
            'Дыхательная настройка' => [
                'description' => 'Глубокое диафрагмальное дыхание и легкое потягивание всего тела. Закройте глаза и сосредоточьтесь только на вдохе и выдохе.',
                'image' => 'warmups/warm-up.png',
            ],
        ];

        $warmups = [];
        foreach ($warmupsData as $name => $data) {
            $warmups[$name] = Warmup::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $data['description'],
                    'image' => $data['image'],
                ]
            );
        }

        $exercisesData = [
            'Приседания' => [
                'muscle_group' => 'Ноги',
                'equipment' => 'Смешанное',
                'description' => 'Базовое упражнение для развития мышц ног и ягодиц. Держите спину ровно, колени не выходят за носки.',
                'image' => 'exercises/training_frame1__card2.png',
            ],
            'Отжимания' => [
                'muscle_group' => 'Грудь',
                'equipment' => 'Смешанное',
                'description' => 'Классические отжимания от пола. При необходимости можно выполнять с колен.',
                'image' => 'exercises/training_frame2_card2.png',
            ],
            'Планка' => [
                'muscle_group' => 'Пресс',
                'equipment' => 'Смешанное',
                'description' => 'Статическое упражнение для мышц кора. Тело должно образовывать прямую линию от пяток до макушки.',
                'image' => 'exercises/training_frame3_card2.png',
            ],
            'Выпады' => [
                'muscle_group' => 'Ноги',
                'equipment' => 'Смешанное',
                'description' => 'Выпады вперед с гантелями или без. Колено передней ноги не выходит за носок, заднее колено касается пола.',
                'image' => 'exercises/training_frame4_card2.png',
            ],
            'Берпи' => [
                'muscle_group' => 'Кардио',
                'equipment' => 'Смешанное',
                'description' => 'Взрывное упражнение: упор лежа, отжимание, прыжок вверх с хлопком.',
                'image' => 'exercises/training_frame2_card1.png',
            ],
            'Скалолаз' => [
                'muscle_group' => 'Кардио',
                'equipment' => 'Смешанное',
                'description' => 'В положении планки поочередно подтягивайте колени к груди в быстром темпе.',
                'image' => 'exercises/training_frame3_card1.png',
            ],
            'Прыжки' => [
                'muscle_group' => 'Кардио',
                'equipment' => 'Смешанное',
                'description' => 'Прыжки на месте или джампинг джеки. Отталкивайтесь носками, мягко приземляйтесь.',
                'image' => 'exercises/training_frame1_card1.png',
            ],
            'Приседания сумо' => [
                'muscle_group' => 'Ноги',
                'equipment' => 'Смешанное',
                'description' => 'Широкая постановка ног, носки развернуты. Приседайте глубоко, держа спину прямой.',
                'image' => 'exercises/training_frame4_card1.png',
            ],
            'Болгарские выпады' => [
                'muscle_group' => 'Ноги',
                'equipment' => 'Смешанное',
                'description' => 'Одна нога сзади на опоре (стул, скамья). Выпады вперед с акцентом на ягодицы.',
                'image' => 'exercises/training_frame1__card2.png',
            ],
            'Ягодичный мостик' => [
                'muscle_group' => 'Ягодицы',
                'equipment' => 'Смешанное',
                'description' => 'Лежа на спине, поднимите таз вверх, сжимая ягодицы в верхней точке.',
                'image' => 'exercises/training_frame2_card2.png',
            ],
            'Поза ребенка' => [
                'muscle_group' => 'Растяжка',
                'equipment' => 'Смешанное',
                'description' => 'Сидя на пятках, наклонитесь вперед, лбом коснитесь пола. Расслабьте спину и плечи.',
                'image' => 'exercises/training_frame3_card1.png',
            ],
            'Складка сидя' => [
                'muscle_group' => 'Растяжка',
                'equipment' => 'Смешанное',
                'description' => 'Сидя на полу с прямыми ногами, наклонитесь к стопам, держа спину ровной.',
                'image' => 'exercises/training_frame4_card1.png',
            ],
            'Скручивания лежа' => [
                'muscle_group' => 'Растяжка',
                'equipment' => 'Смешанное',
                'description' => 'Лежа на спине, согните одну ногу и перекиньте через другую, разворачивая корпус.',
                'image' => 'exercises/training_frame2_card1.png',
            ],
            'Поза голубя' => [
                'muscle_group' => 'Растяжка',
                'equipment' => 'Смешанное',
                'description' => 'Одна нога согнута вперед, другая вытянута назад. Глубокое раскрытие тазобедренных суставов.',
                'image' => 'exercises/training_frame3_card2.png',
            ],
        ];

        $exercises = [];
        foreach ($exercisesData as $title => $data) {
            $equipment = \App\Models\Equipment::where('name', $data['equipment'])->first();
            if (!$equipment) {
                $equipment = \App\Models\Equipment::factory()->create(['name' => $data['equipment']]);
            }
            $exercises[$title] = Exercise::firstOrCreate(
                ['title' => $title],
                [
                    'equipment_id' => $equipment->id,
                    'muscle_group' => $data['muscle_group'],
                    'description' => $data['description'],
                    'image' => $data['image'],
                ]
            );
        }

        $workoutsData = [
            [
                'title' => 'Утренняя перезагрузка',
                'type' => 'functional',
                'duration_minutes' => 20,
                'description' => 'Эффективный комплекс для пробуждения мышц, улучшения кровообращения и повышения тонуса перед рабочим днем.',
                'image' => 'workouts/trainings_card1.png',
                'warmups' => ['Суставная мобилизация'],
                'exercises' => [
                    ['title' => 'Приседания', 'sets' => 3, 'reps' => 15, 'order' => 1],
                    ['title' => 'Отжимания', 'sets' => 3, 'reps' => 12, 'order' => 2],
                    ['title' => 'Планка', 'sets' => 3, 'reps' => 30, 'order' => 3, 'is_seconds' => true], // планка 30 секунд
                    ['title' => 'Выпады', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ],
            ],
            [
                'title' => 'Интенсивное кардио',
                'type' => 'hiit',
                'duration_minutes' => 25,
                'description' => 'Высокоинтенсивная интервальная тренировка для ускорения метаболизма и сжигания калорий.',
                'image' => 'workouts/trainings_card2.png',
                'warmups' => ['Активация пульса'],
                'exercises' => [
                    ['title' => 'Берпи', 'sets' => 4, 'reps' => 10, 'order' => 1],
                    ['title' => 'Скалолаз', 'sets' => 4, 'reps' => 20, 'order' => 2],
                    ['title' => 'Прыжки', 'sets' => 4, 'reps' => 15, 'order' => 3],
                ],
            ],
            [
                'title' => 'Силовая на ноги и ягодицы',
                'type' => 'strength',
                'duration_minutes' => 30,
                'description' => 'Базовые упражнения для проработки квадрицепсов, бицепса бедра и ягодичных мышц.',
                'image' => 'workouts/trainings_card3.png',
                'warmups' => ['Разогрев нижней части'],
                'exercises' => [
                    ['title' => 'Приседания сумо', 'sets' => 3, 'reps' => 15, 'order' => 1],
                    ['title' => 'Болгарские выпады', 'sets' => 3, 'reps' => 12, 'order' => 2],
                    ['title' => 'Ягодичный мостик', 'sets' => 3, 'reps' => 20, 'order' => 3],
                ],
            ],
            [
                'title' => 'Вечерний стретчинг',
                'type' => 'functional',
                'duration_minutes' => 15,
                'description' => 'Мягкая растяжка для снятия напряжения после рабочего дня и улучшения качества сна.',
                'image' => 'workouts/trainings_card4.png',
                'warmups' => ['Дыхательная настройка'],
                'exercises' => [
                    ['title' => 'Поза ребенка', 'sets' => 1, 'reps' => 60, 'order' => 1, 'is_seconds' => true],
                    ['title' => 'Складка сидя', 'sets' => 1, 'reps' => 45, 'order' => 2, 'is_seconds' => true],
                    ['title' => 'Скручивания лежа', 'sets' => 1, 'reps' => 30, 'order' => 3, 'is_seconds' => true],
                    ['title' => 'Поза голубя', 'sets' => 1, 'reps' => 60, 'order' => 4, 'is_seconds' => true],
                ],
            ],
        ];

        foreach ($workoutsData as $workoutData) {
            $workout = Workout::firstOrCreate(
                ['title' => $workoutData['title']],
                [
                    'type' => $workoutData['type'],
                    'duration_minutes' => $workoutData['duration_minutes'],
                    'description' => $workoutData['description'],
                    'image' => $workoutData['image'],
                    'is_active' => true,
                    'phase_id' => \App\Models\Phase::inRandomOrder()->first()?->id ?? 1,
                ]
            );

            if (isset($workoutData['warmups'])) {
                $order = 1;
                foreach ($workoutData['warmups'] as $warmupName) {
                    if (isset($warmups[$warmupName])) {
                        WorkoutWarmup::firstOrCreate(
                            [
                                'workout_id' => $workout->id,
                                'warmup_id' => $warmups[$warmupName]->id,
                            ],
                            ['order_number' => $order++]
                        );
                    }
                }
            }

            if (isset($workoutData['exercises'])) {
                foreach ($workoutData['exercises'] as $exerciseItem) {
                    $exercise = $exercises[$exerciseItem['title']] ?? null;
                    if ($exercise) {
                        $reps = $exerciseItem['reps'];
                        if (isset($exerciseItem['is_seconds']) && $exerciseItem['is_seconds']) {
                        }
                        WorkoutExercise::firstOrCreate(
                            [
                                'workout_id' => $workout->id,
                                'exercise_id' => $exercise->id,
                                'order_number' => $exerciseItem['order'],
                            ],
                            [
                                'sets' => $exerciseItem['sets'],
                                'reps' => $reps,
                            ]
                        );
                    }
                }
            }

            $this->command->info("Тренировка '{$workoutData['title']}' добавлена/обновлена.");
        }
    }
}
