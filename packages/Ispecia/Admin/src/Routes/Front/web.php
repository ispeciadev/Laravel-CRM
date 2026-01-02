<?php

use Illuminate\Support\Facades\Route;
use Ispecia\Admin\Http\Controllers\Controller;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');
