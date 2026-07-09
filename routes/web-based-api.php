<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

Route::get('api/', function () {
    return response()->json([
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::prefix('api')->group(function () {
    Route::middleware('auth')->name('api.')->group(function () {
        Route::get('/contacts', [ContactController::class, 'load'])->name('contacts');
    });
});

