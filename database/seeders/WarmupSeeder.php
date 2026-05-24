<?php

namespace Database\Seeders;

use App\Models\Warmup;
use Illuminate\Database\Seeder;

class WarmupSeeder extends Seeder
{
    private array $warmups = [
        [
            'name' => 'Суставная мобилизация',
            'description' => 'Плавные вращения для шеи, плечевого пояса, позвоночника, коленей и голеностопа. Подготовка связок к нагрузке.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 180,
        ],
        [
            'name' => 'Активация пульса',
            'description' => 'Бег на месте, джампинг джеки, вращения руками для повышения пульса.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 120,
        ],
        [
            'name' => 'Разогрев нижней части',
            'description' => 'Махи ногами, приседания без веса, выпады в сторону для подготовки суставов. Движения должны быть амплитудными, чтобы почувствовать тепло в мышцах.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 180,
        ],
        [
            'name' => 'Дыхательная настройка',
            'description' => 'Глубокое диафрагмальное дыхание и легкое потягивание всего тела. Закройте глаза и сосредоточьтесь только на вдохе и выдохе.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 90,
        ],
        [
            'name' => 'Разогрев верха',
            'description' => 'Вращения плечами, наклоны и повороты корпуса, отжимания от стены. Подготавливает мышцы груди, спины и плечевого пояса к нагрузке.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 180,
        ],
        [
            'name' => 'Разминка плечевых суставов',
            'description' => 'Маятниковые движения руками, вращения в плечевых суставах, динамические растяжки. Снижает риск травмы вращательной манжеты.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 120,
        ],
        [
            'name' => 'Активация мышц спины',
            'description' => 'Супермены, разгибания в пояснице, динамические тяги без веса. Подготавливает разгибатели позвоночника и широчайшие к тяговым упражнениям.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 120,
        ],
        [
            'name' => 'Активация ягодиц',
            'description' => 'Отведения ног, мостики, упражнение "моллюск" с резинкой или без. Пробуждает ягодичные мышцы перед тренировкой нижней части тела.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 120,
        ],
        [
            'name' => 'Активация мышц кора',
            'description' => 'Вакуум, планка 20 секунд, мертвый жук без нагрузки. Устанавливает нейромышечную связь с мышцами кора перед тренировкой.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 90,
        ],
        [
            'name' => 'Динамическая растяжка',
            'description' => 'Амплитудные движения для всего тела: выпады с поворотом, боковые выпады, инча-червь. Увеличивает подвижность суставов без снижения мощности.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 180,
        ],
        [
            'name' => 'Кардио-разогрев (5 минут)',
            'description' => 'Умеренный бег или прыжки со скакалкой в течение 5 минут. Повышает температуру тела и готовит сердечно-сосудистую систему к интенсивной нагрузке.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 300,
        ],
        [
            'name' => 'Разминка перед бегом',
            'description' => 'Высокие колени, захлесты голени, боковые шаги, разминка голеностопа. Специфическая разминка для снижения риска беговых травм.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 180,
        ],
        [
            'name' => 'МФР с роллом',
            'description' => 'Миофасциальный релиз с массажным роллом для квадрицепсов, IT-тракта, спины и икр. Снимает мышечное напряжение и улучшает кровообращение.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 300,
        ],
        [
            'name' => 'Подготовка ЦНС',
            'description' => 'Взрывные движения малой интенсивности: прыжки на месте, хлопки, взрывные приседания без веса. Активирует центральную нервную систему перед силовой тренировкой.',
            'image' => 'warmups/warm-up.png',
            'duration_seconds' => 90,
        ],
    ];

    public function run(): void
    {
        foreach ($this->warmups as $warmupData) {
            Warmup::updateOrCreate(
                ['name' => $warmupData['name']],
                [
                    'description' => $warmupData['description'],
                    'image' => $warmupData['image'],
                    'duration_seconds' => $warmupData['duration_seconds'],
                ]
            );

            $this->command->info("Разминка: {$warmupData['name']}");
        }
    }
}
