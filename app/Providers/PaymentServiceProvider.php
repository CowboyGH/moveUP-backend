<?php

namespace App\Providers;

use App\Services\Billing\CardService;
use App\Services\Billing\PaymentService;
use App\Services\Billing\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CardService::class, function ($app) {
            return new CardService();
        });

        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(CardService::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
