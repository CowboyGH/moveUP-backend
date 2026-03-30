<?php

namespace Database\Seeders;

use App\Models\TestingExercise;
use Illuminate\Database\Seeder;

class TestingExerciseSeeder extends Seeder
{
    private array $realTestingExercises = [
        [
            'title' => '12-минутный бег',
            'description' => 'За 12 минут необходимо пробежать максимально возможную дистанцию. Результат оценивается по таблице возрастных норм. Бег может выполняться на стадионе или беговой дорожке.',
            'image' => 'testing-exercises/cooper-run.jpg'
        ],
        [
            'title' => 'Гарвардский степ-тест',
            'description' => 'В течение 5 минут выполнять восхождения на платформу высотой 50 см для мужчин и 43 см для женщин. Темп: 30 восхождений в минуту.',
            'image' => 'testing-exercises/harvard-step.jpg'
        ],
        [
            'title' => 'Тест Руфье (приседания)',
            'description' => 'Выполнить 30 глубоких приседаний за 45 секунд. Пульс измеряется до нагрузки, сразу после и через минуту восстановления.',
            'image' => 'testing-exercises/ruffier-squat.jpg'
        ],
        [
            'title' => 'Жим лежа (1ПМ)',
            'description' => 'Определение максимального веса в жиме лежа. Выполняются подходы с постепенным увеличением веса до достижения одноповторного максимума.',
            'image' => 'testing-exercises/bench-press-1rm.jpg'
        ],
        [
            'title' => 'Приседания со штангой (1ПМ)',
            'description' => 'Тестирование максимального веса в приседаниях со штангой на спине. Выполняется с соблюдением техники (глубина не менее параллели).',
            'image' => 'testing-exercises/squat-1rm.jpg'
        ],
        [
            'title' => 'Становая тяга (1ПМ)',
            'description' => 'Определение разового максимума в становой тяге. Строгое соблюдение техники: прямая спина, правильный хват.',
            'image' => 'testing-exercises/deadlift-1rm.jpg'
        ],
        [
            'title' => 'Наклон вперед сидя',
            'description' => 'Сидя на полу, ноги прямые, стопы упираются в ящик. Наклониться вперед и зафиксировать положение на 2 секунды.',
            'image' => 'testing-exercises/sit-and-reach.jpg'
        ],
        [
            'title' => 'Планка',
            'description' => 'Удержание планки на локтях. Фиксируется максимальное время с соблюдением правильной техники (прямая линия тела).',
            'image' => 'testing-exercises/plank-test.jpg'
        ],
        [
            'title' => 'Скручивания',
            'description' => 'Максимальное количество скручиваний лежа на спине за 60 секунд. Ноги согнуты, руки за головой.',
            'image' => 'testing-exercises/abs-test.jpg'
        ],
        [
            'title' => 'Гиперэкстензия',
            'description' => 'Удержание туловища на весу в гиперэкстензии. Фиксируется максимальное время.',
            'image' => 'testing-exercises/hyperextension.jpg'
        ],
        [
            'title' => 'Прыжок в длину с места',
            'description' => 'Прыжок с места толчком двух ног. Измеряется расстояние от линии старта до ближайшей точки касания пятками.',
            'image' => 'testing-exercises/long-jump.jpg'
        ],
        [
            'title' => 'Берпи',
            'description' => 'Максимальное количество берпи за 2 минуты. Полная техника: упор лежа, отжимание, прыжок вверх с хлопком.',
            'image' => 'testing-exercises/burpee-test.jpg'
        ],
        [
            'title' => 'Челночный бег 3x10 м',
            'description' => 'Три отрезка по 10 метров с максимальной скоростью. Касание линии на старте и финише.',
            'image' => 'testing-exercises/shuttle-run.jpg'
        ],
        [
            'title' => 'Стойка на одной ноге',
            'description' => 'Стоя на одной ноге с закрытыми глазами, засекается время до потери равновесия.',
            'image' => 'testing-exercises/balance-test.jpg'
        ],
        [
            'title' => 'Отжимания от пола',
            'description' => 'Максимальное количество отжиманий от пола за 60 секунд. Техника: грудь касается пола, полное выпрямление рук.',
            'image' => 'testing-exercises/pushups-test.jpg'
        ],
        [
            'title' => 'Подтягивания',
            'description' => 'Максимальное количество подтягиваний. Хват прямой, подбородок выше перекладины.',
            'image' => 'testing-exercises/pullups-test.jpg'
        ],
    ];

    public function run(): void
    {
        $this->command->info('Создание тестовых упражнений...');

        foreach ($this->realTestingExercises as $exerciseData) {
            TestingExercise::updateOrCreate(
                ['title' => $exerciseData['title']],
                [
                    'description' => $exerciseData['description'],
                    'image' => $exerciseData['image'],
                ]
            );
        }

        TestingExercise::factory()
            ->count(10)
            ->randomData()
            ->create();

        $this->command->info('Создано ' . TestingExercise::count() . ' тестовых упражнений');
    }
}
