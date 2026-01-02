<?php

namespace Ispecia\Voip\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Voip\Contracts\VoipAccount as VoipAccountContract;
use Ispecia\User\Models\User;

class VoipAccount extends Model implements VoipAccountContract
{
    protected $table = 'voip_accounts';

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'extension',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
