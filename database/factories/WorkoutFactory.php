<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkoutFactory extends Factory
{
    private array $realWorkouts = [
        [
            'title' => 'Силовая: Грудь + Трицепс',
            'type' => 'strength',
            'description' => 'Классическая силовая тренировка для развития грудных мышц и трицепса. Включает жимы лежа, разведения гантелей и французские жимы.',
            'duration' => 50,
            'image' => 'workouts/strength-chest-triceps.jpg'
        ],
        [
            'title' => 'Силовая: Спина + Бицепс',
            'type' => 'strength',
            'description' => 'Тренировка для развития мышц спины и бицепса. Тяги штанги в наклоне, подтягивания, сгибания рук с гантелями.',
            'duration' => 50,
            'image' => 'workouts/strength-back-biceps.jpg'
        ],
        [
            'title' => 'Силовая: Ноги',
            'type' => 'strength',
            'description' => 'Базовая силовая тренировка ног. Приседания со штангой, жим ногами, выпады, сгибания и разгибания ног в тренажере.',
            'duration' => 55,
            'image' => 'workouts/strength-legs.jpg'
        ],
        [
            'title' => 'Силовая: Плечи + Трапеции',
            'type' => 'strength',
            'description' => 'Тренировка дельтовидных мышц и трапеций. Жимы гантелей, махи в стороны, тяги к подбородку, шраги.',
            'duration' => 45,
            'image' => 'workouts/strength-shoulders.jpg'
        ],
        [
            'title' => 'Силовая: Мертвая тяга + спина',
            'type' => 'strength',
            'description' => 'Тренировка с акцентом на становую тягу и мышцы спины. Классическая становая, тяга штанги в наклоне, гиперэкстензии.',
            'duration' => 50,
            'image' => 'workouts/strength-deadlift.jpg'
        ],

        [
            'title' => 'Объемная: Грудь и спина',
            'type' => 'hypertrophy',
            'description' => 'Многоповторная тренировка для гипертрофии мышц груди и спины. Работа в диапазоне 8-12 повторений.',
            'duration' => 50,
            'image' => 'workouts/hypertrophy-chest-back.jpg'
        ],
        [
            'title' => 'Объемная: Ноги и ягодицы',
            'type' => 'hypertrophy',
            'description' => 'Высокообъемная тренировка для мышц ног и ягодиц. Приседания, выпады, ягодичный мостик, махи.',
            'duration' => 55,
            'image' => 'workouts/hypertrophy-legs-glutes.jpg'
        ],
        [
            'title' => 'Объемная: Руки',
            'type' => 'hypertrophy',
            'description' => 'Изолированная тренировка для бицепса и трицепса. Различные сгибания и разгибания с гантелями и штангой.',
            'duration' => 45,
            'image' => 'workouts/hypertrophy-arms.jpg'
        ],
        [
            'title' => 'Объемная: Плечи',
            'type' => 'hypertrophy',
            'description' => 'Многоповторная тренировка для дельтовидных мышц. Жимы, махи, подъемы с акцентом на пампинг.',
            'duration' => 45,
            'image' => 'workouts/hypertrophy-shoulders.jpg'
        ],

        [
            'title' => 'HIIT: Жиросжигающая',
            'type' => 'hiit',
            'description' => 'Интервальная тренировка высокой интенсивности. Чередование взрывных упражнений и активного отдыха.',
            'duration' => 25,
            'image' => 'workouts/hiit-fat-burn.jpg'
        ],
        [
            'title' => 'HIIT: Табата протокол',
            'type' => 'hiit',
            'description' => 'Тренировка по протоколу Табата: 20 секунд максимальной работы, 10 секунд отдыха, 8 раундов на каждое упражнение.',
            'duration' => 20,
            'image' => 'workouts/hiit-tabata.jpg'
        ],
        [
            'title' => 'HIIT: Силовой',
            'type' => 'hiit',
            'description' => 'Интервальная тренировка с силовыми упражнениями. Взрывные отжимания, берпи, прыжки, выпады.',
            'duration' => 30,
            'image' => 'workouts/hiit-strength.jpg'
        ],

        [
            'title' => 'Круговая: Full body',
            'type' => 'circuit',
            'description' => 'Круговая тренировка на все группы мышц. 6 упражнений, 3-4 круга, отдых между кругами 2 минуты.',
            'duration' => 40,
            'image' => 'workouts/circuit-fullbody.jpg'
        ],
        [
            'title' => 'Круговая: Жиросжигающая',
            'type' => 'circuit',
            'description' => 'Интенсивная круговая тренировка для сжигания жира. Сочетание силовых и кардио упражнений.',
            'duration' => 35,
            'image' => 'workouts/circuit-fat-burn.jpg'
        ],
        [
            'title' => 'Круговая: Функциональная',
            'type' => 'circuit',
            'description' => 'Круговая тренировка с функциональными упражнениями. Развивает силу, выносливость и координацию.',
            'duration' => 45,
            'image' => 'workouts/circuit-functional.jpg'
        ],

        [
            'title' => 'Функциональный: Core',
            'type' => 'functional',
            'description' => 'Тренировка для укрепления мышц кора. Планки, скручивания, подъемы ног, упражнения на баланс.',
            'duration' => 30,
            'image' => 'workouts/functional-core.jpg'
        ],
        [
            'title' => 'Функциональный: Мобильность',
            'type' => 'functional',
            'description' => 'Тренировка на развитие подвижности суставов и гибкости. Суставная гимнастика, динамическая растяжка.',
            'duration' => 35,
            'image' => 'workouts/functional-mobility.jpg'
        ],
        [
            'title' => 'Функциональный: Равновесие',
            'type' => 'functional',
            'description' => 'Упражнения на развитие координации и равновесия. Работа на нестабильных поверхностях, одноногие движения.',
            'duration' => 30,
            'image' => 'workouts/functional-balance.jpg'
        ],

        [
            'title' => 'Кардио: Интервальный бег',
            'type' => 'cardio',
            'description' => 'Интервальные забеги на дорожке или стадионе. Чередование спринта и легкого бега трусцой.',
            'duration' => 30,
            'image' => 'workouts/cardio-interval-run.jpg'
        ],
        [
            'title' => 'Кардио: Велотренировка',
            'type' => 'cardio',
            'description' => 'Равномерная кардио-сессия на велотренажере с вариацией нагрузки для развития выносливости.',
            'duration' => 45,
            'image' => 'workouts/cardio-bike.jpg'
        ],
    ];

    public function definition(): array
    {
        $workout = $this->faker->randomElement($this->realWorkouts);

        return [
            'phase_id' => Phase::inRandomOrder()->first()?->id ?? Phase::factory(),
            'title' => $workout['title'],
            'type' => $workout['type'],
            'description' => $workout['description'],
            'duration_minutes' => $workout['duration'],
            'image' => $workout['image'],
            'is_active' => true,
        ];
    }

    public function forPhase(Phase $phase): static
    {
        return $this->state(fn (array $attributes) => [
            'phase_id' => $phase->id,
        ]);
    }
}
