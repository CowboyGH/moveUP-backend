<?php

namespace Database\Factories;

use App\Models\TestingExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestingExerciseFactory extends Factory
{
    private array $realTestingExercises = [
        [
            'title' => '12-минутный бег',
            'description' => 'За 12 минут необходимо пробежать максимально возможную дистанцию. Результат оценивается по таблице возрастных норм. Бег может выполняться на стадионе или беговой дорожке.',
            'image' => 'testing-exercises/cooper-run.jpg'
        ],
        [
            'title' => 'Гарвардский степ-тест',
            'description' => 'В течение 5 минут выполнять восхождения на платформу высотой 50 см для мужчин и 43 см для женщин. Темп: 30 восхождений в минуту. После окончания измеряется пульс 3 раза.',
            'image' => 'testing-exercises/harvard-step.jpg'
        ],
        [
            'title' => 'Тест Руфье (приседания)',
            'description' => 'Выполнить 30 глубоких приседаний за 45 секунд. Пульс измеряется до нагрузки, сразу после и через минуту восстановления. Оценивается работа сердца.',
            'image' => 'testing-exercises/ruffier-squat.jpg'
        ],
        [
            'title' => 'Жим лежа (1ПМ)',
            'description' => 'Определение максимального веса в жиме лежа. Выполняются подходы с постепенным увеличением веса до достижения одноповторного максимума. Обязательна страховка.',
            'image' => 'testing-exercises/bench-press-1rm.jpg'
        ],
        [
            'title' => 'Приседания со штангой (1ПМ)',
            'description' => 'Тестирование максимального веса в приседаниях со штангой на спине. Выполняется с соблюдением техники (глубина не менее параллели).',
            'image' => 'testing-exercises/squat-1rm.jpg'
        ],
        [
            'title' => 'Становая тяга (1ПМ)',
            'description' => 'Определение разового максимума в становой тяге. Строгое соблюдение техники: прямая спина, правильный хват, полное выпрямление в финале.',
            'image' => 'testing-exercises/deadlift-1rm.jpg'
        ],
        [
            'title' => 'Отжимания от пола',
            'description' => 'Максимальное количество отжиманий от пола за 60 секунд. Техника: грудь касается пола, полное выпрямление рук в верхней точке, корпус прямой.',
            'image' => 'testing-exercises/pushups-test.jpg'
        ],
        [
            'title' => 'Скручивания на пресс',
            'description' => 'Максимальное количество скручиваний лежа на спине за 60 секунд. Ноги согнуты, руки за головой, поясница прижата к полу.',
            'image' => 'testing-exercises/abs-test.jpg'
        ],
        [
            'title' => 'Подтягивания',
            'description' => 'Максимальное количество подтягиваний без ограничения времени. Хват прямой, ширина хвата - на выбор тестируемого. Подбородок выше перекладины.',
            'image' => 'testing-exercises/pullups-test.jpg'
        ],
        [
            'title' => 'Наклон вперед стоя',
            'description' => 'Стоя на возвышении, наклониться вперед, не сгибая ноги в коленях. Измеряется расстояние от кончиков пальцев до уровня стоп.',
            'image' => 'testing-exercises/forward-bend.jpg'
        ],
        [
            'title' => 'Наклон вперед сидя (Sit and Reach)',
            'description' => 'Сидя на полу, ноги прямые, стопы упираются в ящик. Наклониться вперед и зафиксировать положение на 2 секунды. Измеряется расстояние до стоп.',
            'image' => 'testing-exercises/sit-and-reach.jpg'
        ],
        [
            'title' => 'Мост',
            'description' => 'Из положения лежа прогнуться, опираясь на руки и ноги. Оценивается высота подъема таза и расстояние между руками и пятками.',
            'image' => 'testing-exercises/bridge-test.jpg'
        ],
        [
            'title' => 'Берпи',
            'description' => 'Максимальное количество берпи за 60 секунд. Полная техника: упор лежа, отжимание, прыжок вверх с хлопком.',
            'image' => 'testing-exercises/burpee-test.jpg'
        ],
        [
            'title' => 'Прыжки на скакалке',
            'description' => 'Максимальное количество прыжков через скакалку за 60 секунд. Учитываются только успешные прыжки без сбоев.',
            'image' => 'testing-exercises/jump-rope-test.jpg'
        ],
        [
            'title' => 'Планка',
            'description' => 'Удержание планки на прямых руках или локтях. Фиксируется максимальное время с соблюдением правильной техники (прямая линия тела).',
            'image' => 'testing-exercises/plank-test.jpg'
        ],
        [
            'title' => 'Челночный бег 3x10 м',
            'description' => 'Три отрезка по 10 метров с максимальной скоростью. Касание линии на старте и финише. Оценивается скорость и ловкость.',
            'image' => 'testing-exercises/shuttle-run.jpg'
        ],
        [
            'title' => 'Прыжок в длину с места',
            'description' => 'Прыжок с места толчком двух ног. Измеряется расстояние от линии старта до ближайшей точки касания пятками.',
            'image' => 'testing-exercises/long-jump.jpg'
        ],
        [
            'title' => 'Прыжок вверх с места',
            'description' => 'Определение высоты прыжка вверх с места. Используется специальная лента или измерительная система.',
            'image' => 'testing-exercises/vertical-jump.jpg'
        ],
        [
            'title' => 'Стойка на одной ноге',
            'description' => 'Стоя на одной ноге с закрытыми глазами, засекается время до потери равновесия. Оценивается вестибулярный аппарат.',
            'image' => 'testing-exercises/balance-test.jpg'
        ],
    ];

    public function definition(): array
    {
        $exercise = $this->faker->randomElement($this->realTestingExercises);

        return [
            'title' => $exercise['title'],
            'description' => $exercise['description'],
            'image' => $exercise['image'],
        ];
    }

    /**
     * Создать тестовое упражнение с произвольными данными (для factory)
     */
    public function randomData(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'image' => 'testing-exercises/random-' . $this->faker->uuid() . '.jpg',
        ]);
    }
}
