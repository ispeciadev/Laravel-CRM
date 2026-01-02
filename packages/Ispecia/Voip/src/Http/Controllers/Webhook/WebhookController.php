<?php

namespace Ispecia\Voip\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Services\VoipManager;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    /**
     * Handle generic webhook for any provider.
     */
    public function handle(Request $request, string $driver)
    {
        Log::info("Webhook received for driver: {$driver}");

        try {
            // Get provider by driver
            $provider = $this->voipManager->getProviderByDriver($driver);

            if (!$provider) {
                Log::error("No active provider found for driver: {$driver}");
                return response()->json(['error' => 'Provider not configured'], 404);
            }

            // Validate webhook signature
            if (!$provider->validateWebhookRequest($request)) {
                Log::warning("Invalid webhook signature for driver: {$driver}");
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Parse webhook event
            $event = $provider->parseWebhookEvent($request);
            Log::info("Parsed webhook event", $event);

            // Handle the event based on provider
            $provider->handleStatusUpdate($request->all());

            // Return appropriate response based on provider
            if ($driver === 'twilio') {
                return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>')
                    ->header('Content-Type', 'text/xml');
            } elseif ($driver === 'telnyx') {
                return response()->json(['status' => 'received']);
            } else {
                return response()->json(['status' => 'success']);
            }
        } catch (\Exception $e) {
            Log::error("Webhook processing error for driver {$driver}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}
