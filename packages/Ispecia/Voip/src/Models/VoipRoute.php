<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Voip\Contracts\VoipRoute as VoipRouteContract;

class VoipRoute extends Model implements VoipRouteContract
{
    protected $table = 'voip_routes';

    protected $fillable = [
        'name',
        'did_number',
        'action_type',
        'action_target',
        'voip_trunk_id',
        'priority',
        'is_active'
    ];

    /**
     * Get the trunk that owns the route.
     */
    public function trunk()
    {
        return $this->belongsTo(\Ispecia\Voip\Models\VoipTrunk::class, 'voip_trunk_id');
    }

}
