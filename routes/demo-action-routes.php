<?php

use Illuminate\Support\Facades\Route;
use App\Services\DefaultDataSeeder;
use App\Enums\DefaultSeeder;

Route::post('/make-demo-account', function () {
    DefaultDataSeeder::dataSeed(config('app.demo_mode', false));

    return back()->with('success', true);
})->name('make-demo');

Route::post('/force-history-seed', function () {
    if(config('app.demo_mode', false))
    {
        try {
            DefaultDataSeeder::initiateSeeder('history', DefaultSeeder::tryFrom('history')->seeder());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
})->name('force-history-seed');