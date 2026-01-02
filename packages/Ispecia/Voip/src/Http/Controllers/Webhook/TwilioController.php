<?php

namespace Ispecia\Voip\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Services\VoipManager;
use Illuminate\Support\Facades\Log;
use Ispecia\Voip\Models\VoipCall;
use Ispecia\Voip\Models\VoipAccount;

class TwilioController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    public function voice(Request $request)
    {
        Log::info('Twilio Webhook Request received', ['CallSid' => $request->input('CallSid'), 'CallStatus' => $request->input('CallStatus')]);
        
        try {
            // Get Twilio provider
            $provider = $this->voipManager->getProviderByDriver('twilio');
            
            if (!$provider) {
                Log::error('Twilio provider not configured');
                abort(500, 'Twilio provider not configured');
            }
            
            // Validate webhook signature
            if (!$provider->validateWebhookRequest($request)) {
                Log::warning('Invalid Twilio webhook signature');
                abort(403, 'Invalid webhook signature');
            }
            
            // If call is from a Client (Browser)
            if (isset($request['Caller']) && strpos($request['Caller'], 'client:') !== false) {
                $identity = substr($request['Caller'], 7);
                $voipAccount = VoipAccount::where('username', $identity)->first();
                
                if ($voipAccount) {
                    VoipCall::create([
                        'sid' => $request['CallSid'],
                        'direction' => 'outbound',
                        'status' => 'initiated',
                        'from_number' => $identity,
                        'to_number' => $request['To'],
                        'user_id' => $voipAccount->user_id,
                        'started_at' => now(),
                    ]);
                }
            } else {
                // Inbound call
                VoipCall::create([
                    'sid' => $request['CallSid'],
                    'direction' => 'inbound',
                    'status' => 'ringing',
                    'from_number' => $request['From'],
                    'to_number' => $request['To'],
                    'started_at' => now(),
                ]);
            }

            $response = $provider->handleIncomingCall($request->all());

            return response($response)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            Log::error('Twilio webhook error', ['message' => $e->getMessage()]);
            abort(500, 'Webhook processing failed');
        }
    }

    public function status(Request $request)
    {
        Log::info('Twilio Status Update', $request->all());
        
        try {
            $provider = $this->voipManager->getProviderByDriver('twilio');
            
            if (!$provider) {
                return response()->json(['status' => 'error', 'message' => 'Provider not found'], 404);
            }
            
            // Validate webhook signature
            if (!$provider->validateWebhookRequest($request)) {
                Log::warning('Invalid Twilio status webhook signature');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            }
            
            $provider->handleStatusUpdate($request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Twilio status webhook error', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
