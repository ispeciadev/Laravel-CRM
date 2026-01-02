<?php

return [
    // Recording settings
    'recording' => [
        'enabled' => env('VOIP_RECORDING_ENABLED', true),
        'storage' => env('VOIP_RECORDING_STORAGE', 'local'),
    ],
    
    // Webhook settings
    'webhook_base_url' => env('VOIP_WEBHOOK_URL', config('app.url')),
    
    // Token TTL (in seconds)
    'token_ttl' => env('VOIP_TOKEN_TTL', 3600),
    
    // Legacy Twilio config (deprecated - use database providers instead)
    // Run: php artisan voip:migrate-config to migrate from .env to database
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'app_sid' => env('TWILIO_APP_SID'),
        'api_key' => env('TWILIO_API_KEY'),
        'api_secret' => env('TWILIO_API_SECRET'),
        'number' => env('TWILIO_NUMBER'),
    ],
];
