<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'admin_locale', 'user'], 'prefix' => config('app.admin_path').'/voip', 'namespace' => 'Ispecia\Voip\Http\Controllers\Admin'], function () {
    // Provider Management
    Route::get('providers', 'ProviderController@index')->name('admin.voip.providers.index');
    Route::get('providers/create', 'ProviderController@create')->name('admin.voip.providers.create');
    Route::post('providers', 'ProviderController@store')->name('admin.voip.providers.store');
    Route::get('providers/{id}/edit', 'ProviderController@edit')->name('admin.voip.providers.edit');
    Route::put('providers/{id}', 'ProviderController@update')->name('admin.voip.providers.update');
    Route::delete('providers/{id}', 'ProviderController@destroy')->name('admin.voip.providers.destroy');
    Route::post('providers/{id}/activate', 'ProviderController@activate')->name('admin.voip.providers.activate');
    Route::post('providers/{id}/test', 'ProviderController@test')->name('admin.voip.providers.test');
    Route::post('providers/mass-destroy', 'ProviderController@massDestroy')->name('admin.voip.providers.mass-destroy');
    
    // Trunks
    Route::get('trunks', 'TrunkController@index')->name('admin.voip.trunks.index');
    Route::get('trunks/create', 'TrunkController@create')->name('admin.voip.trunks.create');
    Route::post('trunks', 'TrunkController@store')->name('admin.voip.trunks.store');
    Route::get('trunks/{id}/edit', 'TrunkController@edit')->name('admin.voip.trunks.edit');
    Route::put('trunks/{id}', 'TrunkController@update')->name('admin.voip.trunks.update');
    Route::delete('trunks/{id}', 'TrunkController@destroy')->name('admin.voip.trunks.destroy');
    Route::post('trunks/mass-destroy', 'TrunkController@massDestroy')->name('admin.voip.trunks.mass_destroy');
    
    // Routes
    Route::get('routes', 'RouteController@index')->name('admin.voip.routes.index');
    Route::get('routes/create', 'RouteController@create')->name('admin.voip.routes.create');
    Route::post('routes', 'RouteController@store')->name('admin.voip.routes.store');
    Route::get('routes/{id}/edit', 'RouteController@edit')->name('admin.voip.routes.edit');
    Route::put('routes/{id}', 'RouteController@update')->name('admin.voip.routes.update');
    Route::delete('routes/{id}', 'RouteController@destroy')->name('admin.voip.routes.destroy');
    Route::post('routes/mass-destroy', 'RouteController@massDestroy')->name('admin.voip.routes.mass_destroy');
    
    // Recordings
    Route::get('recordings', 'RecordingController@index')->name('admin.voip.recordings.index');
    Route::get('recordings/{id}/download', 'RecordingController@download')->name('admin.voip.recordings.download');
    Route::delete('recordings/{id}', 'RecordingController@destroy')->name('admin.voip.recordings.destroy');
    
    // Click-to-call
    Route::post('call', '\\Ispecia\\Voip\\Http\\Controllers\\Api\\CallController@initiateOutbound')->name('admin.voip.calls.outbound');

    // Test page
    Route::get('test', '\\Ispecia\\Voip\\Http\\Controllers\\Admin\\VoipTestController@index')->name('admin.voip.test.index');
    Route::post('test', '\\Ispecia\\Voip\\Http\\Controllers\\Admin\\VoipTestController@test')->name('admin.voip.test.run');
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'api/voip', 'namespace' => 'Ispecia\\Voip\\Http\\Controllers\\Api'], function () {
    Route::post('token', 'TokenController@generate')->name('api.voip.token');
    Route::get('client-config', 'TokenController@generate')->name('api.voip.client-config'); // Alias for frontend
    Route::post('calls/outbound', 'CallController@initiateOutbound')->name('api.voip.calls.outbound');
    Route::get('calls/history', 'CallController@history')->name('api.voip.calls.history');
    Route::get('contacts', 'ContactController@index')->name('api.voip.contacts');
});

Route::group(['prefix' => 'voip/webhook', 'namespace' => 'Ispecia\Voip\Http\Controllers\Webhook'], function () {
    // Legacy Twilio-specific routes
    Route::post('twilio/voice', 'TwilioController@voice')->name('voip.webhook.twilio.voice');
    Route::post('twilio/status', 'TwilioController@status')->name('voip.webhook.twilio.status');
    
    // Generic webhook route for multi-provider support
    Route::post('{driver}', 'WebhookController@handle')->name('voip.webhook.generic');
});
