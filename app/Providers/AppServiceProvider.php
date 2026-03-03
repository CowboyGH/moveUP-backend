<?php

namespace App\Providers;

use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneratorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuestDataService::class, function ($app) {
            return new GuestDataService();
        });
        $this->app->singleton(WorkoutGeneratorService::class, function ($app) {
            return new WorkoutGeneratorService();
        });
        $this->app->singleton(PhaseService::class, function ($app) {
            return new PhaseService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
