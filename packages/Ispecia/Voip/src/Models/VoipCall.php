<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Voip\Contracts\VoipCall as VoipCallContract;
use Ispecia\User\Models\User;
use Ispecia\Lead\Models\Lead;
use Ispecia\Deal\Models\Deal;
use Ispecia\Contact\Models\Person;

class VoipCall extends Model implements VoipCallContract
{
    protected $table = 'voip_calls';

    protected $fillable = [
        'sid',
        'direction',
        'status',
        'from_number',
        'to_number',
        'user_id',
        'lead_id',
        'person_id',
        'deal_id',
        'started_at',
        'ended_at',
        'duration'
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    
    public function recordings()
    {
        return $this->hasMany(VoipRecording::class, 'call_id');
    }
}
