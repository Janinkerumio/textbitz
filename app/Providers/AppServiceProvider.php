<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\RouteMacroService;
use App\Services\DefaultDataSeeder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        RouteMacroService::register();

        DefaultDataSeeder::dataSeed(seedDemoData: config('app.demo_mode', false));

        RateLimiter::for('recipient-pushes', function () {
            return Limit::perMinute(100);
        });
    }
}
