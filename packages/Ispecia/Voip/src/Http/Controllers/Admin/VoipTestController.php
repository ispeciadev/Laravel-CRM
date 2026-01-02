<?php

namespace Ispecia\Voip\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Services\VoipManager;

class VoipTestController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    public function index()
    {
        return view('voip::admin.test.index');
    }

    public function test(Request $request)
    {
        $request->validate([
            'to_number' => 'nullable|string',
            'do_call'   => 'nullable|in:1',
        ]);

        $user = auth()->user();
        $identity = 'admin_' . $user->id;

        try {
            $provider = $this->voipManager->getActiveProvider();
            $token = $provider->generateClientToken($identity);

            $result = [
                'token' => $token,
                'provider' => $provider->getProviderModel()->getDriverDisplayName(),
            ];

            if ($request->filled('to_number') && $request->input('do_call') == '1') {
                try {
                    $from = $user->voip_account ? $user->voip_account->username : 'user_' . $user->id;
                    $sid = $provider->initiateCall($from, $request->input('to_number'));
                    $result['call_sid'] = $sid;
                    $result['message'] = $sid ? 'Call initiated' : 'Call failed';
                } catch (\Exception $e) {
                    $result['error'] = $e->getMessage();
                }
            }

            return redirect()->route('admin.voip.test.index')->with('voip_test', $result);
        } catch (\Exception $e) {
            return redirect()->route('admin.voip.test.index')->with('voip_test', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
