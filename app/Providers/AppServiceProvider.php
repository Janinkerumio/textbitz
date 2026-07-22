<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Services\DefaultDataSeeder;
use App\Enums\DefaultSeeder;

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

        Route::macro('page', function ($uri, $page, $props = []) {
            return Route::get($uri, fn () => Inertia::render("{$page}/Main", $props));
        });

        if(config('app.demo_mode'))
        {
            $this->dataSeed();
        }
    }

    public function dataSeed()
    {
        $configured = config('app.initiate_db_seed', []);

        foreach (DefaultSeeder::cases() as $defaultSeeder)
        {
            if(in_array($defaultSeeder->value, $configured, true))
            {
                DefaultDataSeeder::initiateSeeder($defaultSeeder->value, $defaultSeeder->seeder());
            }
        }
    }
}
