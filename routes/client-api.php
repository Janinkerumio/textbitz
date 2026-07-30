<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RecipientsController;
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
        Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
        Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{id}', [ContactController::class, 'delete'])->name('contacts.delete');

        Route::get('/history', [HistoryController::class, 'load'])->name('history');
        Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
        Route::delete('/history/{id}', [HistoryController::class, 'delete'])->name('history.delete');

        Route::get('/templates', [TemplatesController::class, 'load'])->name('templates');
        Route::get('/templates/{id}', [TemplatesController::class, 'show'])->name('templates.show');
        Route::post('/templates', [TemplatesController::class, 'store'])->name('templates.store');
        Route::put('/templates/{id}', [TemplatesController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{id}', [TemplatesController::class, 'delete'])->name('templates.delete');

        Route::get('/recipients/{history_id}', [RecipientsController::class, 'loadByHistory'])->name('recipients.by-history');

        Route::post('/settings/business', [SettingsController::class, 'changeBusinessSettings'])->name('settings.change.business');
    });
});

