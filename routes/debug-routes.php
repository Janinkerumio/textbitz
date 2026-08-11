<?php

use Illuminate\Support\Facades\Route;
use App\Services\Resolvers\PlatformService;
use Illuminate\Support\Facades\File;
use Native\Mobile\Facades\System;
use Native\Mobile\Facades\Device;

if(config('app.debug'))
{
    Route::get('/debug-extensions', function () {
        return response()->json([
            'loadedExtensions' => get_loaded_extensions(),
            'nativeIsLoaded' => PlatformService::isRunningNatively(),
            'platformServiceResponse' => PlatformService::detect(),
            'nativeResponse' => [
                'android' => System::isAndroid(),
                'ios' => System::isIos()
            ],
            'deviceInfo' => Device::getInfo(),
        ]);
    });

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

    Route::get('/debug/logs', function () {
        $path = storage_path('logs/laravel.log');

        if (!File::exists($path)) {
            return response('No logs found.', 404);
        }

        $lines = collect(explode("\n", File::get($path)))
            ->reverse()
            ->take(200)
            ->reverse()
            ->implode("\n");

        return response()->json($lines);
    });
}