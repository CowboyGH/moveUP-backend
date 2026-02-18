<?php
// app/Providers/PaymentServiceProvider.php

namespace App\Providers;

use App\Services\Payment\CardService;
use App\Services\Payment\PaymentService;
use App\Services\Payment\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем сервисы
        $this->app->singleton(CardService::class, function ($app) {
            return new CardService();
        });

        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(CardService::class),
                $app->make(SubscriptionService::class)
            );
        });

        // Для обратной совместимости с 'payment' фасадом
        $this->app->singleton('payment', function ($app) {
            return $app->make(PaymentService::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
