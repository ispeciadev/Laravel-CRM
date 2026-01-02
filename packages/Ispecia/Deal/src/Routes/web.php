<?php

use Illuminate\Support\Facades\Route;
use Ispecia\Deal\Http\Controllers\DealController;

// Align with Admin routes stack (admin path + locale + user middleware)
Route::middleware(['web', 'admin_locale', 'user'])->prefix(config('app.admin_path'))->group(function () {
    Route::controller(DealController::class)->prefix('deals')->group(function () {
        Route::get('', 'index')->name('admin.deals.index');
        Route::get('create', 'create')->name('admin.deals.create');
        Route::post('create', 'store')->name('admin.deals.store');
        Route::get('view/{id}', 'view')->name('admin.deals.view');
        Route::get('edit/{id}', 'edit')->name('admin.deals.edit');
        Route::put('edit/{id}', 'update')->name('admin.deals.update');
        Route::delete('{id}', 'destroy')->name('admin.deals.delete');
        Route::post('mass-destroy', 'massDestroy')->name('admin.deals.mass_delete');
        Route::post('mass-update', 'massUpdate')->name('admin.deals.mass_update');
        Route::get('get/{pipeline_id?}', 'get')->name('admin.deals.get');
    });
});
