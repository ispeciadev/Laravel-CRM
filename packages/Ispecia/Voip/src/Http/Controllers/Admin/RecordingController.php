<?php

namespace Ispecia\Voip\Http\Controllers\Admin;

use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Voip\Models\VoipRecording;
use Ispecia\Voip\DataGrids\RecordingDataGrid;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    public function index()
    {
        if (request()->ajax() || request()->isXmlHttpRequest() || request()->expectsJson()) {
            return datagrid(RecordingDataGrid::class)->process();
        }

        return view('voip::admin.recordings.index');
    }

    public function download($id)
    {
        $recording = VoipRecording::findOrFail($id);
        
        if (!$recording->path) {
            abort(404, 'Recording file not found');
        }
        
        // For Twilio recordings (hosted on Twilio's servers)
        if ($recording->disk === 's3' || str_contains($recording->path, 'twilio.com')) {
            // Redirect to Twilio recording URL with .mp3 extension
            return redirect($recording->path . '.mp3');
        }
        
        // For local recordings
        return Storage::disk($recording->disk)->download($recording->path);
    }

    public function destroy($id)
    {
        $recording = VoipRecording::findOrFail($id);
        $recording->delete();
        
        return response()->json(['message' => 'Recording deleted successfully']);
    }
}
