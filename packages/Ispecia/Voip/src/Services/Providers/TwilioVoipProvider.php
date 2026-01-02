<?php

namespace Ispecia\Voip\Services\Providers;

use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;
use Twilio\Security\RequestValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ispecia\Voip\Models\VoipCall;
use Ispecia\Voip\Models\VoipProvider as VoipProviderModel;
use Ispecia\Voip\Services\VoipProviderInterface;

class TwilioVoipProvider implements VoipProviderInterface
{
    protected $client;
    protected $providerModel;
    protected $config;
    protected $sid;
    protected $token;
    protected $apiKey;
    protected $apiSecret;
    protected $appSid;
    protected $number;

    public function __construct(VoipProviderModel $providerModel)
    {
        $this->providerModel = $providerModel;
        $this->config = $providerModel->config;

        $this->sid = $this->config['account_sid'] ?? null;
        $this->token = $this->config['auth_token'] ?? null;
        $this->apiKey = $this->config['api_key_sid'] ?? null;
        $this->apiSecret = $this->config['api_key_secret'] ?? null;
        $this->appSid = $this->config['app_sid'] ?? null;
        $this->number = $this->config['from_number'] ?? null;

        if ($this->sid && $this->token) {
            $this->client = new Client($this->sid, $this->token);
        }
    }

    public function generateClientToken($identity)
    {
        if (!$this->sid) {
            return null;
        }

        $keySid = $this->apiKey ?: $this->appSid;
        $keySecret = $this->apiSecret ?: $this->token;

        if (!$keySid || !$keySecret) {
            return null;
        }

        $accessToken = new AccessToken(
            $this->sid,
            $keySid,
            $keySecret,
            3600,
            $identity
        );

        $voiceGrant = new VoiceGrant();
        if ($this->appSid) {
            $voiceGrant->setOutgoingApplicationSid($this->appSid);
        }
        $voiceGrant->setIncomingAllow(true);

        $accessToken->addGrant($voiceGrant);

        return $accessToken->toJWT();
    }

    public function initiateCall($from, $to, $params = [])
    {
        if (!$this->client) {
            return null;
        }

        try {
            Log::info('Twilio call attempt', [
                'to' => $to,
                'from_configured' => $this->number ?? null,
                'user_from' => $from,
            ]);

            $call = $this->client->calls->create(
                $to,
                $this->number,
                [
                    'twiml' => '<Response><Dial><Number>' . $to . '</Number></Dial></Response>',
                    'statusCallback' => route('voip.webhook.twilio.status'),
                    'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                    'record' => config('voip.recording.enabled', false) ? 'true' : 'false',
                ]
            );

            Log::info('Twilio call created', ['sid' => $call->sid ?? null]);
            return $call->sid;
        } catch (\Exception $e) {
            Log::error('Twilio Call Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function handleIncomingCall($payload)
    {
        $response = new VoiceResponse();
        $from = $payload['From'] ?? null;
        $to = $payload['To'] ?? null;

        $dial = $response->dial(['callerId' => $this->number]);

        if (isset($payload['To']) && strpos($payload['To'], 'client:') !== false) {
            $dial->client(substr($payload['To'], 7));
        } else {
            $dial->client('admin');
        }

        return $response;
    }

    public function handleStatusUpdate($payload)
    {
        $callSid = $payload['CallSid'] ?? null;
        $status = $payload['CallStatus'] ?? null;

        if ($callSid && $status) {
            $call = VoipCall::where('sid', $callSid)->first();
            if ($call) {
                $call->status = $status;
                if ($status === 'completed') {
                    $call->ended_at = now();
                    $call->duration = $payload['CallDuration'] ?? 0;

                    if (isset($payload['RecordingUrl'])) {
                        $call->recordings()->create([
                            'path' => $payload['RecordingUrl'],
                            'duration' => $payload['RecordingDuration'] ?? 0,
                            'format' => 'mp3',
                            'disk' => 's3'
                        ]);
                    }
                }
                $call->save();
            }
        }
    }

    public function hangupCall(string $providerCallId): void
    {
        if (!$this->client) {
            throw new \RuntimeException('Twilio client not initialized');
        }

        try {
            $this->client->calls($providerCallId)->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('Twilio hangup error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getCallStatus(string $providerCallId): string
    {
        if (!$this->client) {
            throw new \RuntimeException('Twilio client not initialized');
        }

        try {
            $call = $this->client->calls($providerCallId)->fetch();
            return $call->status;
        } catch (\Exception $e) {
            Log::error('Twilio get call status error', ['message' => $e->getMessage()]);
            return 'unknown';
        }
    }

    public function startRecording(string $providerCallId): mixed
    {
        if (!$this->client) {
            throw new \RuntimeException('Twilio client not initialized');
        }

        try {
            $recording = $this->client->calls($providerCallId)
                ->recordings
                ->create();
            return $recording->sid;
        } catch (\Exception $e) {
            Log::error('Twilio start recording error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function stopRecording(string $providerCallId): mixed
    {
        if (!$this->client) {
            throw new \RuntimeException('Twilio client not initialized');
        }

        try {
            $recordings = $this->client->calls($providerCallId)
                ->recordings
                ->read(['status' => 'in-progress']);

            foreach ($recordings as $recording) {
                $this->client->recordings($recording->sid)
                    ->update(['status' => 'stopped']);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Twilio stop recording error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function validateWebhookRequest(Request $request): bool
    {
        if (!$this->token) {
            return false;
        }

        $validator = new RequestValidator($this->token);
        $signature = $request->header('X-Twilio-Signature', '');
        $url = $request->fullUrl();
        $params = $request->all();

        return $validator->validate($signature, $url, $params);
    }

    public function parseWebhookEvent(Request $request): array
    {
        return [
            'call_sid' => $request->input('CallSid'),
            'status' => $request->input('CallStatus'),
            'from' => $request->input('From'),
            'to' => $request->input('To'),
            'duration' => $request->input('CallDuration'),
            'recording_url' => $request->input('RecordingUrl'),
            'recording_duration' => $request->input('RecordingDuration'),
        ];
    }

    public function getClientConfig($user): array
    {
        $voipAccount = $user->voip_account;
        $identity = $voipAccount ? $voipAccount->username : 'user_' . $user->id;

        $token = $this->generateClientToken($identity);

        return [
            'provider' => 'twilio',
            'token' => $token,
            'identity' => $identity,
            'edge' => $this->config['voice_region'] ?? 'ashburn',
        ];
    }

    public function testConnection(): array
    {
        if (!$this->client) {
            return [
                'success' => false,
                'message' => 'Invalid credentials: Account SID or Auth Token missing'
            ];
        }

        try {
            // Try to fetch account details
            $account = $this->client->api->v2010->accounts($this->sid)->fetch();

            return [
                'success' => true,
                'message' => 'Successfully connected to Twilio. Account: ' . $account->friendlyName
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
