<?php

namespace Ispecia\Voip\Http\Controllers\Admin;

use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Models\VoipRoute;
use Ispecia\Voip\Models\VoipTrunk;
use Ispecia\Voip\DataGrids\RouteDataGrid;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        if (request()->ajax() || request()->isXmlHttpRequest() || request()->expectsJson()) {
            return datagrid(RouteDataGrid::class)->process();
        }

        return view('voip::admin.routes.index');
    }

    public function create()
    {
        $trunks = VoipTrunk::where('is_active', true)->orderBy('name')->get();
        return view('voip::admin.routes.create', compact('trunks'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'destination_type' => 'required|string|in:trunk,extension,queue,voicemail',
            'destination' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'trunk_id' => 'nullable|exists:voip_trunks,id',
        ]);
        
        VoipRoute::create($request->only(['name', 'pattern', 'destination_type', 'destination', 'priority', 'is_active', 'trunk_id']));
        
        session()->flash('success', 'Route created successfully');
        
        return redirect()->route('admin.voip.routes.index');
    }

    public function edit($id)
    {
        $route = VoipRoute::findOrFail($id);
        $trunks = VoipTrunk::where('is_active', true)->orderBy('name')->get();
        return view('voip::admin.routes.edit', compact('route', 'trunks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pattern' => 'required|string|max:255',
            'destination_type' => 'nullable|string|in:trunk,extension,queue,voicemail',
            'destination' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'trunk_id' => 'nullable|exists:voip_trunks,id',
        ]);
        
        $route = VoipRoute::findOrFail($id);
        $route->update($request->only(['name', 'pattern', 'destination_type', 'destination', 'priority', 'is_active', 'trunk_id']));
        
        session()->flash('success', 'Route updated successfully');
        
        return redirect()->route('admin.voip.routes.index');
    }

    public function destroy($id)
    {
        $route = VoipRoute::findOrFail($id);
        $route->delete();
        
        return response()->json(['message' => 'Route deleted successfully']);
    }

    public function massDestroy(Request $request)
    {
        $ids = explode(',', $request->input('indices'));
        
        VoipRoute::whereIn('id', $ids)->delete();
        
        return response()->json(['message' => 'Routes deleted successfully']);
    }
}
