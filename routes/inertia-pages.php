<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TemplatesController;

Route::middleware('auth')->name('app.')->group( function () {
    Route::page('/dashboard', 'Home')->name('dashboard');
    Route::action('/contacts', 'Contact')->name('contacts');
    Route::action('/create', 'Blast')->name('blast.create');
    Route::action('/history', 'History')->name('blast.history');
    Route::action('/templates', 'Templates')->name('blast-templates');
    Route::page('/settings', 'Settings')->name('settings');

    //shortcuts-------------------
    Route::get('/template/use/{id}', [TemplatesController::class, 'getAndUseTemplate'])->name('templates.use');

});