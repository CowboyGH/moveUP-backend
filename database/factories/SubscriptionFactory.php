<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    private array $realSubscriptions = [
        [
            'name' => '1 месяц',
            'price' => 500,
            'duration_days' => 30,
            'description' => 'Базовый тариф для знакомства с платформой. Включает доступ ко всем тренировкам и тестам, персональные рекомендации, отслеживание прогресса. Отлично подходит для тех, кто хочет попробовать и оценить возможности сервиса.',
            'image' => 'subscriptions/subscription.png'
        ],
        [
            'name' => '3 месяца',
            'price' => 1400,
            'duration_days' => 90,
            'description' => 'Оптимальный выбор для регулярных тренировок. Включает все возможности базового тарифа + расширенную статистику, анализ прогресса, доступ к эксклюзивным программам тренировок. Экономия 1500₽ по сравнению с помесячной оплатой.',
            'image' => 'subscriptions/subscription.png'
        ],
        [
            'name' => '6 месяцев',
            'price' => 2700,
            'duration_days' => 180,
            'description' => 'Идеальный вариант для достижения серьезных результатов. Все возможности предыдущих тарифов + персональные консультации с тренером, индивидуальная корректировка программ, приоритетная поддержка. Экономия 3300₽ по сравнению с помесячной оплатой.',
            'image' => 'subscriptions/subscription.png'
        ],
        [
            'name' => '12 месяцев',
            'price' => 5000,
            'duration_days' => 365,
            'description' => 'Максимальная выгода для преданных пользователей. Полный доступ ко всем функциям платформы: неограниченные тренировки, персональный план питания, вебинары с экспертами, доступ к закрытому сообществу. Экономия 7000₽ по сравнению с помесячной оплатой.',
            'image' => 'subscriptions/subscription.png'
        ],
    ];

    public function definition(): array
    {
        $subscription = $this->faker->randomElement($this->realSubscriptions);

        return [
            'name' => $subscription['name'],
            'description' => $subscription['description'],
            'price' => $subscription['price'],
            'duration_days' => $subscription['duration_days'],
            'image' => $subscription['image'],
            'is_active' => true,
        ];
    }

    protected function getPriceByName(string $name): float
    {
        return match($name) {
            '1 месяц' => 500,
            '3 месяца' => 1400,
            '6 месяцев' => 2700,
            '12 месяцев' => 5000,
            default => 500,
        };
    }

    protected function getDescriptionByName(string $name): string
    {
        return match($name) {
            '1 месяц' => 'Базовый тариф для знакомства с платформой. Включает доступ ко всем тренировкам и тестам, персональные рекомендации, отслеживание прогресса. Отлично подходит для тех, кто хочет попробовать и оценить возможности сервиса.',
            '3 месяца' => 'Оптимальный выбор для регулярных тренировок. Включает все возможности базового тарифа + расширенную статистику, анализ прогресса, доступ к эксклюзивным программам тренировок. Экономия 1500₽ по сравнению с помесячной оплатой.',
            '6 месяцев' => 'Идеальный вариант для достижения серьезных результатов. Все возможности предыдущих тарифов + персональные консультации с тренером, индивидуальная корректировка программ, приоритетная поддержка. Экономия 3300₽ по сравнению с помесячной оплатой.',
            '12 месяцев' => 'Максимальная выгода для преданных пользователей. Полный доступ ко всем функциям платформы: неограниченные тренировки, персональный план питания, вебинары с экспертами, доступ к закрытому сообществу. Экономия 7000₽ по сравнению с помесячной оплатой.',
            default => 'Подписка на тренировки с доступом ко всем функциям платформы.',
        };
    }

    protected function getImageByName(string $name): string
    {
        return match($name) {
            '1 месяц' => 'subscriptions/subscription.png',
            '3 месяца' => 'subscriptions/subscription.png',
            '6 месяцев' => 'subscriptions/subscription.png',
            '12 месяцев' => 'subscriptions/subscription.png',
            default => 'subscriptions/subscription.png'
        };
    }

    public function basicOneMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '1 месяц',
            'description' => $this->getDescriptionByName('1 месяц'),
            'price' => 500,
            'duration_days' => 30,
            'image' => $this->getImageByName('1 месяц'),
            'is_active' => true,
        ]);
    }

    public function proThreeMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '3 месяца',
            'description' => $this->getDescriptionByName('3 месяца'),
            'price' => 1400,
            'duration_days' => 90,
            'image' => $this->getImageByName('3 месяца'),
            'is_active' => true,
        ]);
    }

    public function premiumSixMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '6 месяцев',
            'description' => $this->getDescriptionByName('6 месяцев'),
            'price' => 2700,
            'duration_days' => 180,
            'image' => $this->getImageByName('6 месяцев'),
            'is_active' => true,
        ]);
    }

    public function ultimateYearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '12 месяцев',
            'description' => $this->getDescriptionByName('12 месяцев'),
            'price' => 5000,
            'duration_days' => 365,
            'image' => $this->getImageByName('12 месяцев'),
            'is_active' => true,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
