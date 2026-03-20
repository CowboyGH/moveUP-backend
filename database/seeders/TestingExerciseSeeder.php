<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\TestingExercise;
use Illuminate\Database\Seeder;

class TestingExerciseSeeder extends Seeder
{
    private array $realTestingExercises = [
        [
            'exercise_title' => '12-минутный бег',
            'description' => 'За 12 минут необходимо пробежать максимально возможную дистанцию. Результат оценивается по таблице возрастных норм. Бег может выполняться на стадионе или беговой дорожке.',
            'image' => 'testing-exercises/cooper-run.jpg'
        ],

        [
            'exercise_title' => 'Степ-тест',
            'description' => 'В течение 5 минут выполнять восхождения на платформу высотой 50 см для мужчин и 43 см для женщин. Темп: 30 восхождений в минуту.',
            'image' => 'testing-exercises/harvard-step.jpg'
        ],

        [
            'exercise_title' => 'Приседания',
            'description' => 'Выполнить 30 глубоких приседаний за 45 секунд. Пульс измеряется до нагрузки, сразу после и через минуту восстановления.',
            'image' => 'testing-exercises/ruffier-squat.jpg'
        ],

        [
            'exercise_title' => 'Жим лежа',
            'description' => 'Определение максимального веса в жиме лежа. Выполняются подходы с постепенным увеличением веса до достижения одноповторного максимума.',
            'image' => 'testing-exercises/bench-press-1rm.jpg'
        ],

        [
            'exercise_title' => 'Приседания со штангой',
            'description' => 'Тестирование максимального веса в приседаниях со штангой на спине. Выполняется с соблюдением техники (глубина не менее параллели).',
            'image' => 'testing-exercises/squat-1rm.jpg'
        ],

        [
            'exercise_title' => 'Становая тяга',
            'description' => 'Определение разового максимума в становой тяге. Строгое соблюдение техники: прямая спина, правильный хват.',
            'image' => 'testing-exercises/deadlift-1rm.jpg'
        ],

        [
            'exercise_title' => 'Наклон вперед сидя',
            'description' => 'Сидя на полу, ноги прямые, стопы упираются в ящик. Наклониться вперед и зафиксировать положение на 2 секунды.',
            'image' => 'testing-exercises/sit-and-reach.jpg'
        ],

        [
            'exercise_title' => 'Планка',
            'description' => 'Удержание планки на локтях. Фиксируется максимальное время с соблюдением правильной техники (прямая линия тела).',
            'image' => 'testing-exercises/plank-test.jpg'
        ],
        [
            'exercise_title' => 'Скручивания',
            'description' => 'Максимальное количество скручиваний лежа на спине за 60 секунд. Ноги согнуты, руки за головой.',
            'image' => 'testing-exercises/abs-test.jpg'
        ],
        [
            'exercise_title' => 'Гиперэкстензия',
            'description' => 'Удержание туловища на весу в гиперэкстензии. Фиксируется максимальное время.',
            'image' => 'testing-exercises/hyperextension.jpg'
        ],

        [
            'exercise_title' => 'Прыжок в длину с места',
            'description' => 'Прыжок с места толчком двух ног. Измеряется расстояние от линии старта до ближайшей точки касания пятками.',
            'image' => 'testing-exercises/long-jump.jpg'
        ],

        [
            'exercise_title' => 'Берпи',
            'description' => 'Максимальное количество берпи за 2 минуты. Полная техника: упор лежа, отжимание, прыжок вверх с хлопком.',
            'image' => 'testing-exercises/burpee-test.jpg'
        ],

        [
            'exercise_title' => 'Челночный бег 3x10 м',
            'description' => 'Три отрезка по 10 метров с максимальной скоростью. Касание линии на старте и финише.',
            'image' => 'testing-exercises/shuttle-run.jpg'
        ],

        [
            'exercise_title' => 'Стойка на одной ноге',
            'description' => 'Стоя на одной ноге с закрытыми глазами, засекается время до потери равновесия.',
            'image' => 'testing-exercises/balance-test.jpg'
        ],
    ];

    public function run(): void
    {
        $exercise = Exercise::all();
        foreach ($this->realTestingExercises as $exerciseData) {
            TestingExercise::updateOrCreate(
                ['description' => $exerciseData['description']],
                [
                    'exercise_id' => $exercise->random()->id,
                    'description' => $exerciseData['description'],
                    'image' => $exerciseData['image'],
                ]
            );
        }

        TestingExercise::factory(10)->create();

        $this->command->info('Создано ' . TestingExercise::count() . ' тестовых упражнений');
    }
}
