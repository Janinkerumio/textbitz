<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlastController;
use App\Http\Controllers\TemplatesController;

Route::middleware('auth')->name('app.')->group( function () {
    Route::action('/dashboard', 'Dashboard')->name('dashboard');
    Route::action('/contacts', 'Contact')->name('contacts');
    Route::action('/create', 'Blast')->name('blast.create');
    Route::action('/history', 'History')->name('blast.history');
    Route::action('/templates', 'Templates')->name('blast-templates');
    Route::page('/settings', 'Settings')->name('settings');

    Route::action('/history/recipients/{id}', 'Recipients')->name('recipients');

    //shortcuts-------------------
    Route::get('/template/use/{id}', [TemplatesController::class, 'getAndUseTemplate'])->name('templates.use');
    Route::get('/blast/duplicate/{id}', [BlastController::class, 'duplicateBlast'])->name('blast.duplicate');

});