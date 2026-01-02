<?php

namespace Ispecia\Voip\Http\Controllers\Admin;

use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Models\VoipTrunk;
use Ispecia\Voip\Models\VoipProvider;
use Ispecia\Voip\DataGrids\TrunkDataGrid;
use Illuminate\Http\Request;

class TrunkController extends Controller
{
    public function index()
    {
        // Support multiple ways the DataGrid may indicate an AJAX/JSON request
        if (request()->ajax() || request()->isXmlHttpRequest() || request()->expectsJson()) {
            return datagrid(TrunkDataGrid::class)->process();
        }

        return view('voip::admin.trunks.index');
    }
    
    public function create()
    {
        $providers = VoipProvider::orderBy('name')->get();
        return view('voip::admin.trunks.create', compact('providers'));
    }
    
    
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'transport' => 'nullable|in:udp,tcp,tls',
            'auth_method' => 'required|in:username_password,ip_auth',
        ];

        // Conditional validation based on auth_method
        if ($request->auth_method === 'username_password') {
            $rules['sip_username'] = 'nullable|string|max:255';
            $rules['sip_password'] = 'nullable|string|min:6';
        } elseif ($request->auth_method === 'ip_auth') {
            $rules['allowed_ips'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Convert allowed_ips from textarea to array if present
        if ($request->auth_method === 'ip_auth' && $request->allowed_ips) {
            $validated['allowed_ips'] = array_filter(
                array_map('trim', explode("\n", $request->allowed_ips))
            );
        }
        
        VoipTrunk::create($validated);
        
        session()->flash('success', 'Trunk created successfully');
        
        return redirect()->route('admin.voip.trunks.index');

    }
    
    public function edit($id)
    {
        $trunk = VoipTrunk::findOrFail($id);
        $providers = VoipProvider::orderBy('name')->get();
        return view('voip::admin.trunks.edit', compact('trunk', 'providers'));
    }
    
    
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'transport' => 'nullable|in:udp,tcp,tls',
            'auth_method' => 'required|in:username_password,ip_auth',
        ];

        // Conditional validation based on auth_method
        if ($request->auth_method === 'username_password') {
            $rules['sip_username'] = 'nullable|string|max:255';
            // Password is optional on update (only if changing)
            $rules['sip_password'] = 'nullable|string|min:6';
        } elseif ($request->auth_method === 'ip_auth') {
            $rules['allowed_ips'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Convert allowed_ips from textarea to array if present
        if ($request->auth_method === 'ip_auth' && $request->allowed_ips) {
            $validated['allowed_ips'] = array_filter(
                array_map('trim', explode("\n", $request->allowed_ips))
            );
        }
        
        $trunk = VoipTrunk::findOrFail($id);
        $trunk->update($validated);
        
        session()->flash('success', 'Trunk updated successfully');
        
        return redirect()->route('admin.voip.trunks.index');

    }
    
    public function destroy($id)
    {
        $trunk = VoipTrunk::findOrFail($id);
        $trunk->delete();
        
        session()->flash('success', 'Trunk deleted successfully');
        
        return response()->json(['message' => 'Trunk deleted successfully']);
    }

    public function massDestroy(Request $request)
    {
        $ids = explode(',', $request->input('indices'));
        
        VoipTrunk::whereIn('id', $ids)->delete();
        
        return response()->json(['message' => 'Trunks deleted successfully']);
    }
}
