<?php

namespace Ispecia\Voip\Services;

use Ispecia\Voip\Models\VoipProvider;
use Illuminate\Support\Facades\Cache;
use Ispecia\Voip\Services\Providers\TwilioVoipProvider;
use Ispecia\Voip\Services\Providers\TelnyxVoipProvider;
use Ispecia\Voip\Services\Providers\SipVoipProvider;

class VoipManager
{
    /**
     * Get the currently active VoIP provider instance.
     *
     * @return VoipProviderInterface
     * @throws \RuntimeException
     */
    public function getActiveProvider(): VoipProviderInterface
    {
        $providerModel = Cache::remember('active_voip_provider', 3600, function () {
            return VoipProvider::active()->first();
        });

        if (!$providerModel) {
            throw new \RuntimeException('No active VoIP provider configured. Please configure a provider in the admin panel.');
        }

        return $this->makeProviderInstance($providerModel);
    }

    /**
     * Get a specific provider by ID.
     *
     * @param int $id
     * @return VoipProviderInterface
     * @throws \RuntimeException
     */
    public function getProviderById(int $id): VoipProviderInterface
    {
        $providerModel = VoipProvider::findOrFail($id);
        
        return $this->makeProviderInstance($providerModel);
    }

    /**
     * Get a provider by driver name.
     *
     * @param string $driver
     * @return VoipProviderInterface|null
     */
    public function getProviderByDriver(string $driver): ?VoipProviderInterface
    {
        $providerModel = VoipProvider::where('driver', $driver)
            ->where('is_active', true)
            ->first();

        if (!$providerModel) {
            return null;
        }

        return $this->makeProviderInstance($providerModel);
    }

    /**
     * Create a provider instance from a model.
     *
     * @param VoipProvider $model
     * @return VoipProviderInterface
     * @throws \RuntimeException
     */
    protected function makeProviderInstance(VoipProvider $model): VoipProviderInterface
    {
        return match ($model->driver) {
            'twilio' => new TwilioVoipProvider($model),
            'telnyx' => new TelnyxVoipProvider($model),
            'sip' => new SipVoipProvider($model),
            default => throw new \RuntimeException("Unsupported VoIP driver: {$model->driver}")
        };
    }

    /**
     * Clear the active provider cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('active_voip_provider');
    }

    /**
     * Get all available provider drivers.
     *
     * @return array
     */
    public function getAvailableDrivers(): array
    {
        return [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Twilio Programmable Voice & WebRTC',
                'fields' => [
                    'account_sid' => ['label' => 'Account SID', 'type' => 'text', 'required' => true],
                    'auth_token' => ['label' => 'Auth Token', 'type' => 'password', 'required' => true],
                    'api_key_sid' => ['label' => 'API Key SID', 'type' => 'text', 'required' => false],
                    'api_key_secret' => ['label' => 'API Key Secret', 'type' => 'password', 'required' => false],
                    'app_sid' => ['label' => 'TwiML App SID', 'type' => 'text', 'required' => false],
                    'from_number' => ['label' => 'From Number', 'type' => 'text', 'required' => true],
                    'voice_region' => ['label' => 'Voice Region', 'type' => 'text', 'required' => false],
                ],
            ],
            'telnyx' => [
                'name' => 'Telnyx',
                'description' => 'Telnyx Voice & WebRTC',
                'fields' => [
                    'api_key' => ['label' => 'API Key', 'type' => 'password', 'required' => true],
                    'connection_id' => ['label' => 'Connection ID', 'type' => 'text', 'required' => true],
                    'from_number' => ['label' => 'From Number', 'type' => 'text', 'required' => true],
                    'webhook_api_secret' => ['label' => 'Webhook API Secret', 'type' => 'password', 'required' => false],
                ],
            ],
            'sip' => [
                'name' => 'Generic SIP',
                'description' => 'Generic SIP Provider (Asterisk, FreeSWITCH, etc.)',
                'fields' => [
                    'sip_server' => ['label' => 'SIP Server', 'type' => 'text', 'required' => true],
                    'sip_port' => ['label' => 'SIP Port', 'type' => 'number', 'required' => false],
                    'username' => ['label' => 'Username', 'type' => 'text', 'required' => true],
                    'password' => ['label' => 'Password', 'type' => 'password', 'required' => true],
                    'transport' => ['label' => 'Transport', 'type' => 'select', 'options' => ['udp', 'tcp', 'tls'], 'required' => false],
                    'from_display_name' => ['label' => 'From Display Name', 'type' => 'text', 'required' => false],
                ],
            ],
        ];
    }

    /**
     * Check if a provider driver is supported.
     *
     * @param string $driver
     * @return bool
     */
    public function isDriverSupported(string $driver): bool
    {
        return array_key_exists($driver, $this->getAvailableDrivers());
    }
}
