<?php

namespace Ispecia\Voip\Http\Controllers\Api;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Services\VoipManager;
use Ispecia\Voip\Models\VoipCall;
use Ispecia\Lead\Models\Lead;
use Ispecia\Contact\Models\Person;
use Ispecia\Deal\Models\Deal;

class CallController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    public function initiateOutbound(Request $request)
    {
        $request->validate([
            'to_number' => 'required',
            'entity_type' => 'nullable|in:lead,person,deal',
            'entity_id' => 'nullable|integer'
        ]);

        $user = auth()->user();
        $from = $user->voip_account ? $user->voip_account->username : 'user_' . $user->id;
        
        try {
            $to = $request->input('to_number');
            
            // Get active provider from VoipManager
            $provider = $this->voipManager->getActiveProvider();

            // Use the provider to create the call
            $callSid = $provider->initiateCall($from, $to, [
                'entity_type' => $request->input('entity_type'),
                'entity_id'   => $request->input('entity_id'),
            ]);

            if ($callSid) {
                return response()->json(['message' => 'Call initiated', 'sid' => $callSid]);
            }

            return response()->json(['message' => 'Failed to initiate call'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error initiating call', 'error' => $e->getMessage()], 500);
        }
    }

    public function history(Request $request)
    {
        $query = VoipCall::with(['user', 'lead', 'person', 'deal', 'recordings'])
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->hasPermission('voip.all_calls')) {
             $query->where('user_id', auth()->id());
        }

        // Apply filters
        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $calls = $query->limit(100)->get()->map(function ($call) {
            // Determine contact name
            $contactName = 'Unknown';
            if ($call->person) {
                $contactName = $call->person->name;
            } elseif ($call->lead && $call->lead->person) {
                $contactName = $call->lead->person->name;
            }

            // Determine phone number based on direction
            $phoneNumber = $call->direction === 'inbound' 
                ? $call->from_number 
                : $call->to_number;

            return [
                'id' => $call->id,
                'sid' => $call->sid,
                'direction' => $call->direction,
                'status' => $call->status,
                'contact_name' => $contactName,
                'phone_number' => $phoneNumber,
                'from_number' => $call->from_number,
                'to_number' => $call->to_number,
                'duration' => $call->duration,
                'started_at' => $call->started_at?->toIso8601String(),
                'ended_at' => $call->ended_at?->toIso8601String(),
                'created_at' => $call->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $calls,
        ]);
    }
}
