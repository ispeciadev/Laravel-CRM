<?php

namespace Ispecia\Voip\Services\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Ispecia\Voip\Models\VoipCall;
use Ispecia\Voip\Models\VoipProvider as VoipProviderModel;
use Ispecia\Voip\Services\VoipProviderInterface;

class TelnyxVoipProvider implements VoipProviderInterface
{
    protected $providerModel;
    protected $config;
    protected $apiKey;
    protected $connectionId;
    protected $fromNumber;
    protected $webhookSecret;
    protected $baseUrl = 'https://api.telnyx.com/v2';

    public function __construct(VoipProviderModel $providerModel)
    {
        $this->providerModel = $providerModel;
        $this->config = $providerModel->config;

        $this->apiKey = $this->config['api_key'] ?? null;
        $this->connectionId = $this->config['connection_id'] ?? null;
        $this->fromNumber = $this->config['from_number'] ?? null;
        $this->webhookSecret = $this->config['webhook_api_secret'] ?? null;
    }

    public function generateClientToken($identity)
    {
        // Telnyx uses WebRTC credentials differently
        // This would typically return a token for Telnyx RTC SDK
        if (!$this->apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/telephony_credentials', [
                'connection_id' => $this->connectionId,
                'name' => $identity,
                'user_to_user' => $identity,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Telnyx token generation error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function initiateCall($from, $to, $params = [])
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            Log::info('Telnyx call attempt', [
                'to' => $to,
                'from' => $this->fromNumber,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/calls', [
                'connection_id' => $this->connectionId,
                'to' => $to,
                'from' => $this->fromNumber,
                'webhook_url' => route('voip.webhook.generic', ['driver' => 'telnyx']),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $callId = $data['data']['call_control_id'] ?? null;
                Log::info('Telnyx call created', ['call_id' => $callId]);
                return $callId;
            }

            Log::error('Telnyx call failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Telnyx Call Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function handleIncomingCall($payload)
    {
        // Telnyx uses JSON responses, not TwiML
        return [
            'actions' => [
                [
                    'action' => 'answer',
                ],
                [
                    'action' => 'speak',
                    'payload' => 'Welcome to the CRM system.',
                ],
            ],
        ];
    }

    public function handleStatusUpdate($payload)
    {
        $callId = $payload['call_control_id'] ?? null;
        $status = $payload['event_type'] ?? null;

        if ($callId && $status) {
            $call = VoipCall::where('sid', $callId)->first();
            if ($call) {
                // Map Telnyx event types to standard statuses
                $mappedStatus = match ($status) {
                    'call.initiated' => 'initiated',
                    'call.answered' => 'answered',
                    'call.hangup' => 'completed',
                    default => $status,
                };

                $call->status = $mappedStatus;
                if ($mappedStatus === 'completed') {
                    $call->ended_at = now();
                    $call->duration = $payload['duration'] ?? 0;
                }
                $call->save();
            }
        }
    }

    public function hangupCall(string $providerCallId): void
    {
        if (!$this->apiKey) {
            throw new \RuntimeException('Telnyx API key not configured');
        }

        try {
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/calls/' . $providerCallId . '/actions/hangup');
        } catch (\Exception $e) {
            Log::error('Telnyx hangup error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getCallStatus(string $providerCallId): string
    {
        if (!$this->apiKey) {
            return 'unknown';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/calls/' . $providerCallId);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['state'] ?? 'unknown';
            }

            return 'unknown';
        } catch (\Exception $e) {
            Log::error('Telnyx get call status error', ['message' => $e->getMessage()]);
            return 'unknown';
        }
    }

    public function startRecording(string $providerCallId): mixed
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/calls/' . $providerCallId . '/actions/record_start', [
                'format' => 'mp3',
                'channels' => 'dual',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Telnyx start recording error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function stopRecording(string $providerCallId): mixed
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/calls/' . $providerCallId . '/actions/record_stop');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telnyx stop recording error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function validateWebhookRequest(Request $request): bool
    {
        if (!$this->webhookSecret) {
            // If no secret configured, skip validation (not recommended for production)
            return true;
        }

        $signature = $request->header('Telnyx-Signature-Ed25519');
        $timestamp = $request->header('Telnyx-Timestamp');
        $body = $request->getContent();

        if (!$signature || !$timestamp) {
            return false;
        }

        // Telnyx uses Ed25519 signature verification
        // This is a simplified version - implement proper Ed25519 verification in production
        $expectedSignature = hash_hmac('sha256', $timestamp . $body, $this->webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    public function parseWebhookEvent(Request $request): array
    {
        $payload = $request->input('data.payload', []);

        return [
            'call_sid' => $payload['call_control_id'] ?? null,
            'status' => $request->input('data.event_type'),
            'from' => $payload['from'] ?? null,
            'to' => $payload['to'] ?? null,
            'duration' => $payload['duration'] ?? null,
        ];
    }

    public function getClientConfig($user): array
    {
        $voipAccount = $user->voip_account;
        $identity = $voipAccount ? $voipAccount->username : 'user_' . $user->id;

        $token = $this->generateClientToken($identity);

        return [
            'provider' => 'telnyx',
            'token' => $token,
            'identity' => $identity,
            'connection_id' => $this->connectionId,
        ];
    }

    public function testConnection(): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'Invalid credentials: API Key missing'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/phone_numbers');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Telnyx'
                ];
            }

            return [
                'success' => false,
                'message' => 'Connection failed: ' . $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    public function getProviderModel()
    {
        return $this->providerModel;
    }
}
