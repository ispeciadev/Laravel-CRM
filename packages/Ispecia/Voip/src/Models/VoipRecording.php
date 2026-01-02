<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Voip\Contracts\VoipRecording as VoipRecordingContract;
use Illuminate\Support\Facades\Storage;

class VoipRecording extends Model implements VoipRecordingContract
{
    protected $table = 'voip_recordings';

    protected $fillable = [
        'call_id',
        'path',
        'disk',
        'format',
        'duration'
    ];

    public function call()
    {
        return $this->belongsTo(VoipCall::class, 'call_id');
    }
    
    public function getUrlAttribute()
    {
        if ($this->disk === 'local' || $this->disk === 'public') {
            return Storage::disk($this->disk)->url($this->path);
        }
        
        return $this->path; // If S3 or external URL
    }
}
