<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Voip\Contracts\VoipTrunk as VoipTrunkContract;

class VoipTrunk extends Model implements VoipTrunkContract
{
    protected $table = 'voip_trunks';

    protected $fillable = [
        'name',
        'provider',
        'voip_provider_id',
        'auth_method',
        'sip_username',
        'sip_password',
        'allowed_ips',
        'host',
        'username',
        'password',
        'port',
        'transport',
        'is_active',
        'priority'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sip_password' => 'encrypted',
        'allowed_ips' => 'array',
        'is_active' => 'boolean',
    ];


    /**
     * Get the provider that owns the trunk.
     */
    public function provider()
    {
        return $this->belongsTo(\Ispecia\Voip\Models\VoipProvider::class, 'voip_provider_id');
    }

    /**
     * Get the routes for the trunk.
     */
    public function routes()
    {
        return $this->hasMany(\Ispecia\Voip\Models\VoipRoute::class, 'voip_trunk_id');
    }

}
