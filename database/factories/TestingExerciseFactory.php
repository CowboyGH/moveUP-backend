<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Testing;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestingExerciseFactory extends Factory
{
    private array $realTestingExercises = [
        [
            'description' => 'За 12 минут необходимо пробежать максимально возможную дистанцию. Результат оценивается по таблице возрастных норм. Бег может выполняться на стадионе или беговой дорожке.',
            'image' => 'testing-exercises/cooper-run.jpg'
        ],
        [
            'description' => 'В течение 5 минут выполнять восхождения на платформу высотой 50 см для мужчин и 43 см для женщин. Темп: 30 восхождений в минуту. После окончания измеряется пульс 3 раза.',
            'image' => 'testing-exercises/harvard-step.jpg'
        ],
        [
            'description' => 'Выполнить 30 глубоких приседаний за 45 секунд. Пульс измеряется до нагрузки, сразу после и через минуту восстановления. Оценивается работа сердца.',
            'image' => 'testing-exercises/ruffier-squat.jpg'
        ],

        [
            'description' => 'Определение максимального веса в жиме лежа. Выполняются подходы с постепенным увеличением веса до достижения одноповторного максимума. Обязательна страховка.',
            'image' => 'testing-exercises/bench-press-1rm.jpg'
        ],
        [
            'description' => 'Тестирование максимального веса в приседаниях со штангой на спине. Выполняется с соблюдением техники (глубина не менее параллели).',
            'image' => 'testing-exercises/squat-1rm.jpg'
        ],
        [
            'description' => 'Определение разового максимума в становой тяге. Строгое соблюдение техники: прямая спина, правильный хват, полное выпрямление в финале.',
            'image' => 'testing-exercises/deadlift-1rm.jpg'
        ],

        [
            'description' => 'Максимальное количество отжиманий от пола за 60 секунд. Техника: грудь касается пола, полное выпрямление рук в верхней точке, корпус прямой.',
            'image' => 'testing-exercises/pushups-test.jpg'
        ],
        [
            'description' => 'Максимальное количество скручиваний лежа на спине за 60 секунд. Ноги согнуты, руки за головой, поясница прижата к полу.',
            'image' => 'testing-exercises/abs-test.jpg'
        ],
        [
            'description' => 'Максимальное количество подтягиваний без ограничения времени. Хват прямой, ширина хвата - на выбор тестируемого. Подбородок выше перекладины.',
            'image' => 'testing-exercises/pullups-test.jpg'
        ],

        [
            'description' => 'Стоя на возвышении, наклониться вперед, не сгибая ноги в коленях. Измеряется расстояние от кончиков пальцев до уровня стоп.',
            'image' => 'testing-exercises/forward-bend.jpg'
        ],
        [
            'description' => 'Сидя на полу, ноги прямые, стопы упираются в ящик. Наклониться вперед и зафиксировать положение на 2 секунды. Измеряется расстояние до стоп.',
            'image' => 'testing-exercises/sit-and-reach.jpg'
        ],
        [
            'description' => 'Из положения лежа прогнуться, опираясь на руки и ноги. Оценивается высота подъема таза и расстояние между руками и пятками.',
            'image' => 'testing-exercises/bridge-test.jpg'
        ],

        [
            'description' => 'Максимальное количество берпи за 60 секунд. Полная техника: упор лежа, отжимание, прыжок вверх с хлопком.',
            'image' => 'testing-exercises/burpee-test.jpg'
        ],
        [
            'description' => 'Максимальное количество прыжков через скакалку за 60 секунд. Учитываются только успешные прыжки без сбоев.',
            'image' => 'testing-exercises/jump-rope-test.jpg'
        ],
        [
            'description' => 'Удержание планки на прямых руках или локтях. Фиксируется максимальное время с соблюдением правильной техники (прямая линия тела).',
            'image' => 'testing-exercises/plank-test.jpg'
        ],

        [
            'description' => 'Три отрезка по 10 метров с максимальной скоростью. Касание линии на старте и финише. Оценивается скорость и ловкость.',
            'image' => 'testing-exercises/shuttle-run.jpg'
        ],
        [
            'description' => 'Прыжок с места толчком двух ног. Измеряется расстояние от линии старта до ближайшей точки касания пятками.',
            'image' => 'testing-exercises/long-jump.jpg'
        ],
        [
            'description' => 'Определение высоты прыжка вверх с места. Используется специальная лента или измерительная система.',
            'image' => 'testing-exercises/vertical-jump.jpg'
        ],
        [
            'description' => 'Стоя на одной ноге с закрытыми глазами, засекается время до потери равновесия. Оценивается вестибулярный аппарат.',
            'image' => 'testing-exercises/balance-test.jpg'
        ],
    ];

    public function definition(): array
    {
        $exercise = $this->faker->randomElement($this->realTestingExercises);

        $baseExercise = Exercise::inRandomOrder()->first()
            ?? Exercise::factory()->create();

        return [
            'exercise_id' => $baseExercise->id,
            'description' => $exercise['description'],
            'image' => $exercise['image'],
        ];
    }
}
