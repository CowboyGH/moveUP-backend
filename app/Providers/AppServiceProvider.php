<?php

namespace App\Providers;

use App\Services\ExerciseLoadService;
use App\Services\GuestDataService;
use App\Services\PhaseService;
use App\Services\WorkoutGeneration\Selector\GoalBasedWorkoutSelector;
use App\Services\WorkoutGeneration\Selector\WorkoutSelectorInterface;
use App\Services\WorkoutGeneration\WorkoutGeneratorService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuestDataService::class);
        $this->app->singleton(ExerciseLoadService::class);
        $this->app->singleton(PhaseService::class);
        $this->app->singleton(WorkoutGeneratorService::class);

        $this->app->bind(WorkoutSelectorInterface::class, GoalBasedWorkoutSelector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth-attempts', fn(Request $r) =>
            Limit::perMinute(5)->by($r->input('email') . '|' . $r->ip())
        );

        RateLimiter::for('auth-codes', fn(Request $r) =>
            Limit::perMinutes(5, 3)->by($r->input('email') . '|' . $r->ip())
        );
    }
}
