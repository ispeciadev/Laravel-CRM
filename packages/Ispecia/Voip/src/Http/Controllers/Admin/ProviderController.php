<?php

namespace Ispecia\Voip\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Models\VoipProvider;
use Ispecia\Voip\Services\VoipManager;
use Ispecia\Voip\DataGrids\ProviderDataGrid;

class ProviderController extends Controller
{
    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        $this->voipManager = $voipManager;
    }

    /**
     * Display a listing of VoIP providers.
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProviderDataGrid::class)->toJson();
        }

        return view('voip::admin.providers.index');
    }

    /**
     * Show the form for creating a new provider.
     */
    public function create()
    {
        $drivers = $this->voipManager->getAvailableDrivers();

        return view('voip::admin.providers.create', compact('drivers'));
    }

    /**
     * Store a newly created provider.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'driver' => 'required|string|in:twilio,telnyx,sip',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $provider = VoipProvider::create([
                'name' => $request->name,
                'driver' => $request->driver,
                'config' => $request->config,
                'is_active' => false,
                'priority' => $request->priority ?? 0,
            ]);

            // Validate provider-specific config
            $errors = $provider->validateConfig();
            if (!empty($errors)) {
                $provider->delete();
                return redirect()->back()
                    ->withErrors($errors)
                    ->withInput();
            }

            session()->flash('success', trans('voip::app.admin.providers.create-success'));

            return redirect()->route('admin.voip.providers.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified provider.
     */
    public function edit(int $id)
    {
        $provider = VoipProvider::findOrFail($id);
        $drivers = $this->voipManager->getAvailableDrivers();

        return view('voip::admin.providers.edit', compact('provider', 'drivers'));
    }

    /**
     * Update the specified provider.
     */
    public function update(Request $request, int $id)
    {
        $provider = VoipProvider::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'driver' => 'nullable|string|in:twilio,telnyx,sip',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $provider->update([
                'name' => $request->name,
                'driver' => $request->driver,
                'config' => $request->config,
                'priority' => $request->priority ?? $provider->priority,
            ]);

            // Validate provider-specific config
            $errors = $provider->validateConfig();
            if (!empty($errors)) {
                return redirect()->back()
                    ->withErrors($errors)
                    ->withInput();
            }

            session()->flash('success', trans('voip::app.admin.providers.update-success'));

            return redirect()->route('admin.voip.providers.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified provider.
     */
    public function destroy(int $id)
    {
        $provider = VoipProvider::findOrFail($id);

        if ($provider->is_active) {
            return response()->json([
                'message' => trans('voip::app.admin.providers.delete-active-error')
            ], 400);
        }

        try {
            $provider->delete();

            return response()->json([
                'message' => trans('voip::app.admin.providers.delete-success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate a provider.
     */
    public function activate(int $id)
    {
        try {
            $provider = VoipProvider::findOrFail($id);
            
            // Validate config before activating
            $errors = $provider->validateConfig();
            if (!empty($errors)) {
                return response()->json([
                    'message' => trans('voip::app.admin.providers.activate-validation-error'),
                    'errors' => $errors
                ], 400);
            }

            $provider->activate();

            return response()->json([
                'message' => trans('voip::app.admin.providers.activate-success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test provider connection.
     */
    public function test(int $id)
    {
        try {
            $provider = $this->voipManager->getProviderById($id);
            $result = $provider->testConnection();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mass delete providers.
     */
    public function massDestroy(Request $request)
    {
        $providerIds = $request->input('indices', []);

        if (empty($providerIds)) {
            return response()->json([
                'message' => 'No providers selected'
            ], 400);
        }

        try {
            // Check if any active providers are being deleted
            $activeCount = VoipProvider::whereIn('id', $providerIds)
                ->where('is_active', true)
                ->count();

            if ($activeCount > 0) {
                return response()->json([
                    'message' => trans('voip::app.admin.providers.delete-active-error')
                ], 400);
            }

            VoipProvider::whereIn('id', $providerIds)->delete();

            return response()->json([
                'message' => trans('voip::app.admin.providers.delete-success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
