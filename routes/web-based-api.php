<?php

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
        Route::get('/contacts/{id}', [ContactController::class, 'show'])->name('contacts.show');
        Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
    });
});

