<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Phase;
use App\Models\Warmup;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutWarmup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WorkoutSeeder extends Seeder
{
    private array $workouts = [
        // ─── Phase 1: Подготовительная ───────────────────────────────────────
        [
            'phase_order' => 1,
            'title' => 'Вводная тренировка',
            'type' => 'general',
            'duration_minutes' => 30,
            'description' => 'Первая тренировка для знакомства с основными движениями. Выполняем упражнения с минимальным весом, отрабатываем технику.',
            'image' => 'workouts/intro-workout.jpg',
            'warmups' => ['Суставная мобилизация'],
            'exercises' => [
                ['title' => 'Приседания без веса', 'sets' => 3, 'reps' => 12, 'order' => 1],
                ['title' => 'Отжимания', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Планка', 'sets' => 3, 'reps' => 30, 'order' => 3],
                ['title' => 'Выпады', 'sets' => 3, 'reps' => 10, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 1,
            'title' => 'Техника приседаний',
            'type' => 'general',
            'duration_minutes' => 35,
            'description' => 'Детальный разбор техники приседаний: положение штанги, глубина, дыхание. Работаем с пустым грифом или легкими весами.',
            'image' => 'workouts/squat-technique.jpg',
            'warmups' => ['Суставная мобилизация', 'Активация ягодиц'],
            'exercises' => [
                ['title' => 'Приседания без веса', 'sets' => 3, 'reps' => 15, 'order' => 1],
                ['title' => 'Приседания', 'sets' => 3, 'reps' => 12, 'order' => 2],
                ['title' => 'Болгарские выпады', 'sets' => 3, 'reps' => 10, 'order' => 3],
                ['title' => 'Ягодичный мостик', 'sets' => 3, 'reps' => 15, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 1,
            'title' => 'Техника жимов',
            'type' => 'general',
            'duration_minutes' => 35,
            'description' => 'Изучение техники жимов лежа и стоя. Положение локтей, траектория движения, дыхание.',
            'image' => 'workouts/press-technique.jpg',
            'warmups' => ['Разогрев верха', 'Разминка плечевых суставов'],
            'exercises' => [
                ['title' => 'Отжимания от пола', 'sets' => 3, 'reps' => 12, 'order' => 1],
                ['title' => 'Жим гантелей сидя', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Сведение рук в кроссовере', 'sets' => 3, 'reps' => 12, 'order' => 3],
                ['title' => 'Подъем гантелей на бицепс', 'sets' => 3, 'reps' => 10, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 1,
            'title' => 'Техника становой тяги',
            'type' => 'general',
            'duration_minutes' => 35,
            'description' => 'Освоение правильной техники становой тяги: настрой, хват, положение спины, подъем и опускание штанги.',
            'image' => 'workouts/deadlift-technique.jpg',
            'warmups' => ['Активация мышц спины', 'Активация мышц кора'],
            'exercises' => [
                ['title' => 'Румынская тяга', 'sets' => 3, 'reps' => 12, 'order' => 1],
                ['title' => 'Гиперэкстензия', 'sets' => 3, 'reps' => 12, 'order' => 2],
                ['title' => 'Тяга гантели в наклоне', 'sets' => 3, 'reps' => 10, 'order' => 3],
                ['title' => 'Мертвый жук', 'sets' => 3, 'reps' => 10, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 1,
            'title' => 'Кардио-адаптация',
            'type' => 'cardio',
            'duration_minutes' => 25,
            'description' => 'Легкая кардио-тренировка для подготовки сердечно-сосудистой системы к нагрузкам.',
            'image' => 'workouts/cardio-adaptation.jpg',
            'warmups' => ['Кардио-разогрев (5 минут)'],
            'exercises' => [
                ['title' => 'Бег на дорожке', 'sets' => 4, 'reps' => 5, 'order' => 1],
                ['title' => 'Велотренажер', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Скакалка', 'sets' => 3, 'reps' => 20, 'order' => 3],
            ],
        ],

        // ─── Phase 2: Базовая ─────────────────────────────────────────────────
        [
            'phase_order' => 2,
            'title' => 'Силовая: Грудь + трицепс',
            'type' => 'strength',
            'duration_minutes' => 50,
            'description' => 'Силовая тренировка на мышцы груди и трицепса. Акцент на базовых жимах с тяжелым весом.',
            'image' => 'workouts/strength-chest-triceps.jpg',
            'warmups' => ['Подготовка ЦНС', 'Разогрев верха'],
            'exercises' => [
                ['title' => 'Жим штанги лежа', 'sets' => 4, 'reps' => 8, 'order' => 1],
                ['title' => 'Жим гантелей на наклонной скамье', 'sets' => 4, 'reps' => 8, 'order' => 2],
                ['title' => 'Отжимания на брусьях', 'sets' => 3, 'reps' => 8, 'order' => 3],
                ['title' => 'Сведение рук в кроссовере', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Французский жим лежа', 'sets' => 3, 'reps' => 12, 'order' => 5],
                ['title' => 'Разгибания на трицепс в блоке', 'sets' => 3, 'reps' => 12, 'order' => 6],
            ],
        ],
        [
            'phase_order' => 2,
            'title' => 'Силовая: Спина + бицепс',
            'type' => 'strength',
            'duration_minutes' => 50,
            'description' => 'Силовая тренировка на мышцы спины и бицепса. Становая тяга, тяги в наклоне, изолирующие упражнения на бицепс.',
            'image' => 'workouts/strength-back-biceps.jpg',
            'warmups' => ['Подготовка ЦНС', 'Активация мышц спины'],
            'exercises' => [
                ['title' => 'Становая тяга', 'sets' => 4, 'reps' => 6, 'order' => 1],
                ['title' => 'Подтягивания', 'sets' => 4, 'reps' => 8, 'order' => 2],
                ['title' => 'Тяга штанги в наклоне', 'sets' => 3, 'reps' => 10, 'order' => 3],
                ['title' => 'Тяга нижнего блока', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Подъем гантелей на бицепс', 'sets' => 3, 'reps' => 12, 'order' => 5],
                ['title' => 'Молотковые сгибания', 'sets' => 3, 'reps' => 12, 'order' => 6],
            ],
        ],
        [
            'phase_order' => 2,
            'title' => 'Силовая: Ноги + плечи',
            'type' => 'strength',
            'duration_minutes' => 50,
            'description' => 'Силовая тренировка на мышцы ног и плеч. Комплекс базовых упражнений для нижней части тела и дельтовидных мышц.',
            'image' => 'workouts/strength-legs-shoulders.jpg',
            'warmups' => ['Подготовка ЦНС', 'Разогрев нижней части', 'Активация ягодиц'],
            'exercises' => [
                ['title' => 'Приседания со штангой', 'sets' => 4, 'reps' => 8, 'order' => 1],
                ['title' => 'Жим ногами', 'sets' => 4, 'reps' => 10, 'order' => 2],
                ['title' => 'Румынская тяга', 'sets' => 3, 'reps' => 10, 'order' => 3],
                ['title' => 'Армейский жим', 'sets' => 4, 'reps' => 8, 'order' => 4],
                ['title' => 'Махи гантелями в стороны', 'sets' => 3, 'reps' => 15, 'order' => 5],
            ],
        ],
        [
            'phase_order' => 2,
            'title' => 'Силовая: База 5x5',
            'type' => 'strength',
            'duration_minutes' => 40,
            'description' => 'Классическая силовая программа 5x5: 5 подходов по 5 повторений в базовых упражнениях.',
            'image' => 'workouts/strength-5x5.jpg',
            'warmups' => ['Подготовка ЦНС', 'Суставная мобилизация'],
            'exercises' => [
                ['title' => 'Приседания со штангой', 'sets' => 5, 'reps' => 5, 'order' => 1],
                ['title' => 'Жим штанги лежа', 'sets' => 5, 'reps' => 5, 'order' => 2],
                ['title' => 'Становая тяга', 'sets' => 5, 'reps' => 5, 'order' => 3],
                ['title' => 'Подтягивания', 'sets' => 5, 'reps' => 5, 'order' => 4],
                ['title' => 'Армейский жим', 'sets' => 5, 'reps' => 5, 'order' => 5],
            ],
        ],

        // ─── Phase 3: Интенсивная ─────────────────────────────────────────────
        [
            'phase_order' => 3,
            'title' => 'Объемная: Грудь',
            'type' => 'hypertrophy',
            'duration_minutes' => 50,
            'description' => 'Объемная тренировка на мышцы груди. Высокий объем и среднее число повторений для максимального роста мышц.',
            'image' => 'workouts/hypertrophy-chest.jpg',
            'warmups' => ['Разогрев верха', 'Динамическая растяжка'],
            'exercises' => [
                ['title' => 'Жим штанги лежа', 'sets' => 4, 'reps' => 10, 'order' => 1],
                ['title' => 'Жим гантелей на наклонной скамье', 'sets' => 4, 'reps' => 10, 'order' => 2],
                ['title' => 'Сведение рук в кроссовере', 'sets' => 4, 'reps' => 12, 'order' => 3],
                ['title' => 'Отжимания от пола', 'sets' => 3, 'reps' => 15, 'order' => 4],
                ['title' => 'Отжимания узким хватом', 'sets' => 3, 'reps' => 12, 'order' => 5],
            ],
        ],
        [
            'phase_order' => 3,
            'title' => 'Объемная: Спина',
            'type' => 'hypertrophy',
            'duration_minutes' => 50,
            'description' => 'Объемная тренировка на мышцы спины. Широкий набор упражнений для комплексной проработки всех отделов.',
            'image' => 'workouts/hypertrophy-back.jpg',
            'warmups' => ['Активация мышц спины', 'Динамическая растяжка'],
            'exercises' => [
                ['title' => 'Подтягивания', 'sets' => 4, 'reps' => 10, 'order' => 1],
                ['title' => 'Тяга верхнего блока', 'sets' => 4, 'reps' => 12, 'order' => 2],
                ['title' => 'Тяга гантели в наклоне', 'sets' => 4, 'reps' => 12, 'order' => 3],
                ['title' => 'Тяга нижнего блока', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Гиперэкстензия', 'sets' => 3, 'reps' => 15, 'order' => 5],
            ],
        ],
        [
            'phase_order' => 3,
            'title' => 'Объемная: Ноги',
            'type' => 'hypertrophy',
            'duration_minutes' => 55,
            'description' => 'Объемная тренировка на мышцы ног. Комплекс базовых и изолирующих упражнений для максимальной нагрузки на квадрицепсы, бицепс бедра и ягодицы.',
            'image' => 'workouts/hypertrophy-legs.jpg',
            'warmups' => ['Разогрев нижней части', 'Активация ягодиц'],
            'exercises' => [
                ['title' => 'Приседания со штангой', 'sets' => 4, 'reps' => 10, 'order' => 1],
                ['title' => 'Жим ногами', 'sets' => 4, 'reps' => 12, 'order' => 2],
                ['title' => 'Болгарские выпады', 'sets' => 3, 'reps' => 12, 'order' => 3],
                ['title' => 'Приседания сумо', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Ягодичный мостик на одной ноге', 'sets' => 3, 'reps' => 12, 'order' => 5],
                ['title' => 'Подъем ног в висе', 'sets' => 3, 'reps' => 15, 'order' => 6],
            ],
        ],
        [
            'phase_order' => 3,
            'title' => 'Объемная: Плечи + руки',
            'type' => 'hypertrophy',
            'duration_minutes' => 45,
            'description' => 'Объемная тренировка на дельтовидные мышцы, бицепс и трицепс. Акцент на изолирующих упражнениях для максимального кровенаполнения.',
            'image' => 'workouts/hypertrophy-shoulders-arms.jpg',
            'warmups' => ['Разминка плечевых суставов', 'Разогрев верха'],
            'exercises' => [
                ['title' => 'Жим гантелей сидя', 'sets' => 4, 'reps' => 10, 'order' => 1],
                ['title' => 'Махи гантелями в стороны', 'sets' => 4, 'reps' => 15, 'order' => 2],
                ['title' => 'Махи в наклоне на заднюю дельту', 'sets' => 4, 'reps' => 15, 'order' => 3],
                ['title' => 'Подъем гантелей на бицепс', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Молотковые сгибания', 'sets' => 3, 'reps' => 12, 'order' => 5],
                ['title' => 'Французский жим лежа', 'sets' => 3, 'reps' => 12, 'order' => 6],
                ['title' => 'Разгибания на трицепс в блоке', 'sets' => 3, 'reps' => 12, 'order' => 7],
            ],
        ],
        [
            'phase_order' => 3,
            'title' => 'Объемная: Фулл-боди',
            'type' => 'hypertrophy',
            'duration_minutes' => 50,
            'description' => 'Объемная тренировка всего тела. Проработка основных мышечных групп за одну сессию с умеренным весом и высоким числом повторений.',
            'image' => 'workouts/hypertrophy-fullbody.jpg',
            'warmups' => ['Суставная мобилизация', 'Динамическая растяжка'],
            'exercises' => [
                ['title' => 'Приседания со штангой', 'sets' => 3, 'reps' => 10, 'order' => 1],
                ['title' => 'Жим штанги лежа', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Тяга штанги в наклоне', 'sets' => 3, 'reps' => 10, 'order' => 3],
                ['title' => 'Жим гантелей сидя', 'sets' => 3, 'reps' => 12, 'order' => 4],
                ['title' => 'Подъем ног в висе', 'sets' => 3, 'reps' => 12, 'order' => 5],
                ['title' => 'Планка', 'sets' => 3, 'reps' => 45, 'order' => 6],
            ],
        ],

        // ─── Phase 4: Фаза отдыха ─────────────────────────────────────────────
        [
            'phase_order' => 4,
            'title' => 'HIIT: Спринты',
            'type' => 'hiit',
            'duration_minutes' => 25,
            'description' => 'Высокоинтенсивная интервальная тренировка на основе спринтов. Чередование максимального усилия и активного отдыха.',
            'image' => 'workouts/hiit-sprints.jpg',
            'warmups' => ['Кардио-разогрев (5 минут)', 'Активация пульса'],
            'exercises' => [
                ['title' => 'Бег на дорожке', 'sets' => 8, 'reps' => 30, 'order' => 1],
                ['title' => 'Берпи', 'sets' => 4, 'reps' => 10, 'order' => 2],
                ['title' => 'Скалолаз', 'sets' => 4, 'reps' => 20, 'order' => 3],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Кардио-силовая',
            'type' => 'hiit',
            'duration_minutes' => 30,
            'description' => 'Тренировка, сочетающая кардио и силовые упражнения. Чередование высокоинтенсивных кардио-блоков с функциональными движениями.',
            'image' => 'workouts/cardio-strength.jpg',
            'warmups' => ['Активация пульса', 'Кардио-разогрев (5 минут)'],
            'exercises' => [
                ['title' => 'Берпи', 'sets' => 5, 'reps' => 8, 'order' => 1],
                ['title' => 'Выпады с гантелями', 'sets' => 5, 'reps' => 12, 'order' => 2],
                ['title' => 'Прыжки', 'sets' => 5, 'reps' => 20, 'order' => 3],
                ['title' => 'Скалолаз', 'sets' => 5, 'reps' => 20, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Круговая жиросжигающая',
            'type' => 'circuit',
            'duration_minutes' => 35,
            'description' => 'Круговая тренировка для жиросжигания. Упражнения выполняются друг за другом с минимальным отдыхом между станциями.',
            'image' => 'workouts/circuit-fat-burn.jpg',
            'warmups' => ['Активация пульса', 'Динамическая растяжка'],
            'exercises' => [
                ['title' => 'Приседания', 'sets' => 4, 'reps' => 15, 'order' => 1],
                ['title' => 'Отжимания', 'sets' => 4, 'reps' => 12, 'order' => 2],
                ['title' => 'Подтягивания', 'sets' => 4, 'reps' => 6, 'order' => 3],
                ['title' => 'Берпи', 'sets' => 4, 'reps' => 10, 'order' => 4],
                ['title' => 'Планка', 'sets' => 4, 'reps' => 40, 'order' => 5],
                ['title' => 'Скакалка', 'sets' => 4, 'reps' => 30, 'order' => 6],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Кардио: Интервальная',
            'type' => 'cardio',
            'duration_minutes' => 30,
            'description' => 'Интервальная кардио-тренировка с чередованием умеренного и высокого темпа. Развивает аэробную выносливость и ускоряет метаболизм.',
            'image' => 'workouts/cardio-interval.jpg',
            'warmups' => ['Кардио-разогрев (5 минут)', 'Разминка перед бегом'],
            'exercises' => [
                ['title' => 'Бег на дорожке', 'sets' => 6, 'reps' => 5, 'order' => 1],
                ['title' => 'Велотренажер', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Скакалка', 'sets' => 3, 'reps' => 30, 'order' => 3],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Активное восстановление',
            'type' => 'functional',
            'duration_minutes' => 30,
            'description' => 'Легкие упражнения для улучшения кровообращения и ускорения восстановления мышц после интенсивных тренировок.',
            'image' => 'workouts/active-recovery.jpg',
            'warmups' => ['МФР с роллом', 'Дыхательная настройка'],
            'exercises' => [
                ['title' => 'Кошка-корова', 'sets' => 3, 'reps' => 10, 'order' => 1],
                ['title' => 'Поза ребенка', 'sets' => 2, 'reps' => 60, 'order' => 2],
                ['title' => 'Поза голубя', 'sets' => 2, 'reps' => 60, 'order' => 3],
                ['title' => 'Мертвый жук', 'sets' => 3, 'reps' => 10, 'order' => 4],
                ['title' => 'Велотренажер', 'sets' => 2, 'reps' => 10, 'order' => 5],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Растяжка',
            'type' => 'functional',
            'duration_minutes' => 40,
            'description' => 'Комплекс упражнений на растяжку всех мышечных групп для улучшения гибкости и восстановления.',
            'image' => 'workouts/stretching.jpg',
            'warmups' => ['Дыхательная настройка'],
            'exercises' => [
                ['title' => 'Поза ребенка', 'sets' => 2, 'reps' => 60, 'order' => 1],
                ['title' => 'Складка сидя', 'sets' => 2, 'reps' => 45, 'order' => 2],
                ['title' => 'Скручивания лежа', 'sets' => 2, 'reps' => 30, 'order' => 3],
                ['title' => 'Поза голубя', 'sets' => 2, 'reps' => 60, 'order' => 4],
                ['title' => 'Кошка-корова', 'sets' => 2, 'reps' => 10, 'order' => 5],
            ],
        ],
        [
            'phase_order' => 4,
            'title' => 'Йога',
            'type' => 'functional',
            'duration_minutes' => 45,
            'description' => 'Упражнения из йоги для развития гибкости, баланса и ментального расслабления.',
            'image' => 'workouts/yoga.jpg',
            'warmups' => ['Дыхательная настройка', 'Активация мышц кора'],
            'exercises' => [
                ['title' => 'Кошка-корова', 'sets' => 3, 'reps' => 10, 'order' => 1],
                ['title' => 'Поза ребенка', 'sets' => 1, 'reps' => 60, 'order' => 2],
                ['title' => 'Поза голубя', 'sets' => 1, 'reps' => 60, 'order' => 3],
                ['title' => 'Складка сидя', 'sets' => 1, 'reps' => 45, 'order' => 4],
                ['title' => 'Планка', 'sets' => 2, 'reps' => 30, 'order' => 5],
                ['title' => 'Мертвый жук', 'sets' => 2, 'reps' => 10, 'order' => 6],
            ],
        ],

        // ─── Phase 5: Продвинутая ─────────────────────────────────────────────
        [
            'phase_order' => 5,
            'title' => 'Силовая на ноги и ягодицы',
            'type' => 'strength',
            'duration_minutes' => 30,
            'description' => 'Базовые упражнения для проработки квадрицепсов, бицепса бедра и ягодичных мышц.',
            'image' => 'workouts/trainings_card3.png',
            'warmups' => ['Разогрев нижней части', 'Активация ягодиц'],
            'exercises' => [
                ['title' => 'Приседания сумо', 'sets' => 3, 'reps' => 15, 'order' => 1],
                ['title' => 'Болгарские выпады', 'sets' => 3, 'reps' => 12, 'order' => 2],
                ['title' => 'Ягодичный мостик', 'sets' => 3, 'reps' => 20, 'order' => 3],
            ],
        ],
        [
            'phase_order' => 5,
            'title' => 'Утренняя перезагрузка',
            'type' => 'functional',
            'duration_minutes' => 20,
            'description' => 'Эффективный комплекс для пробуждения мышц, улучшения кровообращения и повышения тонуса перед рабочим днем.',
            'image' => 'workouts/trainings_card1.png',
            'warmups' => ['Суставная мобилизация'],
            'exercises' => [
                ['title' => 'Приседания', 'sets' => 3, 'reps' => 15, 'order' => 1],
                ['title' => 'Отжимания', 'sets' => 3, 'reps' => 12, 'order' => 2],
                ['title' => 'Планка', 'sets' => 3, 'reps' => 30, 'order' => 3],
                ['title' => 'Выпады', 'sets' => 3, 'reps' => 12, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 5,
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
            'phase_order' => 5,
            'title' => 'Табата-тренировка',
            'type' => 'hiit',
            'duration_minutes' => 20,
            'description' => 'Тренировка по протоколу Табата: 20 секунд максимального усилия, 10 секунд отдыха, 8 раундов на каждое упражнение.',
            'image' => 'workouts/tabata.jpg',
            'warmups' => ['Активация пульса'],
            'exercises' => [
                ['title' => 'Приседания без веса', 'sets' => 8, 'reps' => 20, 'order' => 1],
                ['title' => 'Отжимания', 'sets' => 8, 'reps' => 20, 'order' => 2],
                ['title' => 'Скалолаз', 'sets' => 8, 'reps' => 20, 'order' => 3],
                ['title' => 'Прыжки', 'sets' => 8, 'reps' => 20, 'order' => 4],
            ],
        ],
        [
            'phase_order' => 5,
            'title' => 'Круговая: Функциональная',
            'type' => 'circuit',
            'duration_minutes' => 45,
            'description' => 'Продвинутая круговая тренировка с функциональными упражнениями. Объединяет силовые, кардио и гимнастические движения в единый комплекс.',
            'image' => 'workouts/circuit-functional.jpg',
            'warmups' => ['Активация пульса', 'Активация мышц кора'],
            'exercises' => [
                ['title' => 'Подтягивания', 'sets' => 4, 'reps' => 6, 'order' => 1],
                ['title' => 'Отжимания на брусьях', 'sets' => 4, 'reps' => 8, 'order' => 2],
                ['title' => 'Выпады с гантелями', 'sets' => 4, 'reps' => 10, 'order' => 3],
                ['title' => 'Берпи', 'sets' => 4, 'reps' => 10, 'order' => 4],
                ['title' => 'Подъем ног в висе', 'sets' => 4, 'reps' => 10, 'order' => 5],
                ['title' => 'Скакалка', 'sets' => 4, 'reps' => 30, 'order' => 6],
            ],
        ],
        [
            'phase_order' => 5,
            'title' => 'Кардио: Велотренировка',
            'type' => 'cardio',
            'duration_minutes' => 45,
            'description' => 'Кардио-тренировка на велотренажере. Низкая ударная нагрузка на суставы, высокая эффективность для сердечно-сосудистой системы и жиросжигания.',
            'image' => 'workouts/cardio-bike.jpg',
            'warmups' => ['Разминка перед бегом'],
            'exercises' => [
                ['title' => 'Велотренажер', 'sets' => 3, 'reps' => 15, 'order' => 1],
                ['title' => 'Бег на дорожке', 'sets' => 3, 'reps' => 10, 'order' => 2],
                ['title' => 'Скакалка', 'sets' => 3, 'reps' => 30, 'order' => 3],
            ],
        ],
    ];

    public function run(): void
    {
        $exercises = Exercise::all()->keyBy('title');
        $warmups = Warmup::all()->keyBy('name');
        $phases = Phase::all()->keyBy('order_number');

        foreach ($this->workouts as $workoutData) {
            $phase = $phases->get($workoutData['phase_order']);
            if (!$phase) {
                throw new RuntimeException("WorkoutSeeder: фаза с order_number={$workoutData['phase_order']} не найдена.");
            }

            DB::transaction(function () use ($workoutData, $phase, $warmups, $exercises) {
                $workout = Workout::updateOrCreate(
                    ['title' => $workoutData['title']],
                    [
                        'phase_id' => $phase->id,
                        'type' => $workoutData['type'],
                        'description' => $workoutData['description'],
                        'duration_minutes' => $workoutData['duration_minutes'],
                        'image' => $workoutData['image'],
                        'is_active' => true,
                    ]
                );

                WorkoutWarmup::where('workout_id', $workout->id)->delete();
                $warmupOrder = 1;
                foreach ($workoutData['warmups'] as $warmupName) {
                    $warmup = $warmups->get($warmupName);
                    if (!$warmup) {
                        throw new RuntimeException("WorkoutSeeder: разминка '{$warmupName}' не найдена. Добавь её в WarmupSeeder.");
                    }
                    WorkoutWarmup::create([
                        'workout_id' => $workout->id,
                        'warmup_id' => $warmup->id,
                        'order_number' => $warmupOrder++,
                    ]);
                }

                WorkoutExercise::where('workout_id', $workout->id)->delete();
                foreach ($workoutData['exercises'] as $exerciseItem) {
                    $exercise = $exercises->get($exerciseItem['title']);
                    if (!$exercise) {
                        throw new RuntimeException("WorkoutSeeder: упражнение '{$exerciseItem['title']}' не найдено. Добавь его в ExerciseSeeder.");
                    }
                    WorkoutExercise::create([
                        'workout_id' => $workout->id,
                        'exercise_id' => $exercise->id,
                        'sets' => $exerciseItem['sets'],
                        'reps' => $exerciseItem['reps'],
                        'order_number' => $exerciseItem['order'],
                    ]);
                }
            });

            $this->command->info("Тренировка '{$workoutData['title']}' создана/обновлена.");
        }

        $this->command->info('Итого тренировок: ' . Workout::count());
    }
}
