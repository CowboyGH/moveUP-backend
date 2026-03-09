<?php

namespace App\Providers;

use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\Selector\GoalBasedWorkoutSelector;
use App\Services\WorkoutGeneration\Selector\WorkoutSelectorInterface;
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
        $this->app->singleton(PhaseService::class, function ($app) {
            return new PhaseService();
        });
        $this->app->bind(WorkoutSelectorInterface::class, GoalBasedWorkoutSelector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
