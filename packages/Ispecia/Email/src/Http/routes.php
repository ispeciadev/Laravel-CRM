<?php

use Illuminate\Support\Facades\Route;
use Ispecia\Email\Http\Controllers\EmailTrackingController;

/**
 * Email tracking routes (public, no auth required)
 */
Route::get('email/track/{hash}', [EmailTrackingController::class, 'track'])
    ->name('email.track');
