<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->name('app.')->group( function () {
    Route::page('/dashboard', 'Home')->name('dashboard');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::page('/create', 'Blast')->name('blast.create');
    Route::page('/history', 'History')->name('blast.history');
    Route::page('/templates', 'Templates')->name('blast-templates');
    Route::page('/settings', 'Settings')->name('settings');
});