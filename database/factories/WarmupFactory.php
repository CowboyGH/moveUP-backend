<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarmupFactory extends Factory
{
    private array $realWarmups = [
        [
            'name' => 'Суставная гимнастика: Полная',
            'description' => 'Комплексная разминка всех суставов: шея, плечи, локти, кисти, позвоночник, тазобедренные, колени, голеностоп. Выполняется сверху вниз.',
            'image' => 'warmups/joint-gymnastics-full.jpg'
        ],
        [
            'name' => 'Суставная гимнастика: Верх тела',
            'description' => 'Разминка суставов верхней части тела: вращения головой, плечами, локтями, кистями, скручивания позвоночника.',
            'image' => 'warmups/joint-gymnastics-upper.jpg'
        ],
        [
            'name' => 'Суставная гимнастика: Ниж тела',
            'description' => 'Разминка суставов нижней части тела: вращения тазом, коленями, голеностопами, махи ногами.',
            'image' => 'warmups/joint-gymnastics-lower.jpg'
        ],

        [
            'name' => 'Динамическая растяжка: Все тело',
            'description' => 'Активная растяжка с движением: махи ногами, выпады с поворотом, наклоны, круговые движения руками.',
            'image' => 'warmups/dynamic-stretching-full.jpg'
        ],
        [
            'name' => 'Динамическая растяжка: Ноги',
            'description' => 'Растяжка мышц ног в движении: махи вперед/назад/в стороны, выпады с пружиной, "складка" на ходу.',
            'image' => 'warmups/dynamic-stretching-legs.jpg'
        ],
        [
            'name' => 'Динамическая растяжка: Спина и плечи',
            'description' => 'Растяжка мышц спины и плечевого пояса: круговые движения, "мельница", наклоны с поворотом.',
            'image' => 'warmups/dynamic-stretching-back.jpg'
        ],

        [
            'name' => 'Кардио: Легкий бег 5 минут',
            'description' => 'Легкая кардио-разминка: бег трусцой на дорожке или на месте с постепенным увеличением темпа.',
            'image' => 'warmups/cardio-light-run.jpg'
        ],
        [
            'name' => 'Кардио: Скакалка',
            'description' => 'Разминка со скакалкой: 2 минуты прыжков на двух ногах, затем по 30 секунд на каждой ноге.',
            'image' => 'warmups/cardio-jump-rope.jpg'
        ],
        [
            'name' => 'Кардио: Велосипед',
            'description' => 'Разминка на велотренажере: 5-7 минут с постепенным повышением нагрузки.',
            'image' => 'warmups/cardio-bike.jpg'
        ],

        [
            'name' => 'Мобилизация: Тазобедренные суставы',
            'description' => 'Упражнения для раскрытия тазобедренных суставов: "лягушка", "голубь", вращения тазом, глубокие выпады.',
            'image' => 'warmups/mobility-hips.jpg'
        ],
        [
            'name' => 'Мобилизация: Грудной отдел',
            'description' => 'Разминка грудного отдела позвоночника: скручивания, разгибания с валиком, раскрытие грудной клетки.',
            'image' => 'warmups/mobility-thoracic.jpg'
        ],
        [
            'name' => 'Мобилизация: Плечевые суставы',
            'description' => 'Упражнения для подвижности плеч: вращения, круги руками, "замки" за спиной, с палкой.',
            'image' => 'warmups/mobility-shoulders.jpg'
        ],

        [
            'name' => 'Активация: Мышцы кора',
            'description' => 'Активация глубоких мышц живота и спины: планка, вакуум, мертвый жук, ягодичный мостик.',
            'image' => 'warmups/activation-core.jpg'
        ],
        [
            'name' => 'Активация: Ягодицы',
            'description' => 'Активация ягодичных мышц: махи ногами, "ракушка", ягодичный мостик на одной ноге.',
            'image' => 'warmups/activation-glutes.jpg'
        ],
        [
            'name' => 'Активация: Мышцы спины',
            'description' => 'Активация мышц спины: лодочка, супермен, тяги резины, разгибания на полу.',
            'image' => 'warmups/activation-back.jpg'
        ],

        [
            'name' => 'Дыхание: Диафрагмальное',
            'description' => 'Обучение диафрагмальному дыханию: глубокие вдохи животом, задержки дыхания, медленные выдохи.',
            'image' => 'warmups/breathing-diaphragm.jpg'
        ],
        [
            'name' => 'Дыхание: Капалабхати',
            'description' => 'Очистительное дыхание: короткие активные выдохи, пассивные вдохи. Разогревает и тонизирует.',
            'image' => 'warmups/breathing-kapalabhati.jpg'
        ],

        [
            'name' => 'ЦНС: Взрывная активация',
            'description' => 'Подготовка нервной системы к работе: легкие прыжки, ускорения, реакция на хлопок, координационные упражнения.',
            'image' => 'warmups/cns-activation.jpg'
        ],

        [
            'name' => 'Разминка с эспандером',
            'description' => 'Разминка с использованием резинового эспандера: отведения рук, вращения, растяжка с сопротивлением.',
            'image' => 'warmups/resistance-band.jpg'
        ],
        [
            'name' => 'Разминка с роллом (МФР)',
            'description' => 'Миофасциальный релиз с массажным роллом: прокатка основных мышечных групп.',
            'image' => 'warmups/mfr-roller.jpg'
        ],
        [
            'name' => 'Разминка перед бегом',
            'description' => 'Специализированная разминка для бегунов: упражнения на беговые мышцы, специальные беговые упражнения.',
            'image' => 'warmups/running-warmup.jpg'
        ],
        [
            'name' => 'Разминка перед силовой',
            'description' => 'Комплексная разминка перед силовой тренировкой: суставы + активация + разогрев.',
            'image' => 'warmups/strength-warmup.jpg'
        ],
    ];

    public function definition(): array
    {
        $warmup = $this->faker->randomElement($this->realWarmups);

        return [
            'name' => $warmup['name'],
            'description' => $warmup['description'],
            'image' => $warmup['image'],
        ];
    }
}
