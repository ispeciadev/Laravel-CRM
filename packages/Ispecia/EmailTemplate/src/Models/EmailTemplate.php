<?php

namespace Ispecia\EmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\EmailTemplate\Contracts\EmailTemplate as EmailTemplateContract;

class EmailTemplate extends Model implements EmailTemplateContract
{
    protected $fillable = [
        'name',
        'subject',
        'content',
    ];
}
