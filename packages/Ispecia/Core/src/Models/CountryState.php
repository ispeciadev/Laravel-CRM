<?php

namespace Ispecia\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Core\Contracts\CountryState as CountryStateContract;

class CountryState extends Model implements CountryStateContract
{
    public $timestamps = false;
}
