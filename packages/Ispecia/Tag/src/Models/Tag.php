<?php

namespace Ispecia\Tag\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Tag\Contracts\Tag as TagContract;
use Ispecia\User\Models\UserProxy;

class Tag extends Model implements TagContract
{
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'user_id',
    ];

    /**
     * Get the user that owns the tag.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }
}
