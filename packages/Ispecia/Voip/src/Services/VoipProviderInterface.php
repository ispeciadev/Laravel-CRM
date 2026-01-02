<?php

namespace Ispecia\Voip\Services;

use Illuminate\Http\Request;

interface VoipProviderInterface
{
    /**
     * Generate client token for WebRTC
     * 
     * @param string $identity User identifier
     * @return string
     */
    public function generateClientToken($identity);

    /**
     * Initiate an outbound call
     * 
     * @param string $from Caller number or SIP
     * @param string $to Callee number
     * @param array $params Additional parameters
     * @return mixed
     */
    public function initiateCall($from, $to, $params = []);

    /**
     * Handle incoming webhook for voice
     * 
     * @param array $payload
     * @return mixed
     */
    public function handleIncomingCall($payload);
    
    /**
     * Handle status update webhook
     * 
     * @param array $payload
     * @return void
     */
    public function handleStatusUpdate($payload);

    /**
     * Hang up an active call
     * 
     * @param string $providerCallId Provider-specific call ID
     * @return void
     */
    public function hangupCall(string $providerCallId): void;

    /**
     * Get call status from provider
     * 
     * @param string $providerCallId Provider-specific call ID
     * @return string
     */
    public function getCallStatus(string $providerCallId): string;

    /**
     * Start recording a call
     * 
     * @param string $providerCallId Provider-specific call ID
     * @return mixed
     */
    public function startRecording(string $providerCallId): mixed;

    /**
     * Stop recording a call
     * 
     * @param string $providerCallId Provider-specific call ID
     * @return mixed
     */
    public function stopRecording(string $providerCallId): mixed;

    /**
     * Validate webhook request signature
     * 
     * @param Request $request
     * @return bool
     */
    public function validateWebhookRequest(Request $request): bool;

    /**
     * Parse webhook event data
     * 
     * @param Request $request
     * @return array
     */
    public function parseWebhookEvent(Request $request): array;

    /**
     * Get client configuration for frontend SDK
     * 
     * @param \Ispecia\User\Models\User $user
     * @return array
     */
    public function getClientConfig($user): array;

    /**
     * Test connection to provider
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection(): array;

    /**
     * Get the provider model instance
     * 
     * @return \Ispecia\Voip\Models\VoipProvider
     */
    public function getProviderModel();
}
