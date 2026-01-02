<?php

namespace Ispecia\Voip\Http\Controllers\Api;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Services\VoipManager;
use Ispecia\Voip\Models\VoipAccount;

class TokenController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    public function generate(Request $request)
    {
        $user = auth()->user();
        
        try {
            // Get active provider
            $provider = $this->voipManager->getActiveProvider();
            
            // Get client configuration from provider
            $config = $provider->getClientConfig($user);
            
            if (!$config || empty($config['token'])) {
                return response()->json(['error' => 'Could not generate token'], 500);
            }
            
            return response()->json($config);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get VoIP configuration',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
