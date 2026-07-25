<?php

use Illuminate\Support\Facades\Route;
use App\Services\PlatformService;

if(config('app.debug'))
{
    Route::get('/debug-extensions', fn () => response()->json(get_loaded_extensions()));
    Route::get('/debug-native-extensions', function () {
        return response()->json([
            'nativeIsLoaded' => PlatformService::isRunningNatively(),
        ]);
    });

    Route::get('/debug/seeder-logs/history', function () {
        $path = storage_path('logs/history-seeder.log');

        if (! file_exists($path)) {
            return response()->json(['lines' => []]);
        }

        $lines = collect(file($path, FILE_IGNORE_NEW_LINES))
            ->reverse()
            ->take(200)
            ->reverse()
            ->values();

        return response()->json(['lines' => $lines]);
    });
}