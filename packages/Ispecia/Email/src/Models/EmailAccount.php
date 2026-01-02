<?php

namespace Ispecia\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_name',
        'default_cc',
        'default_bcc',
        'is_default',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'port'       => 'integer',
    ];

    /**
     * Set the password attribute (encrypt it).
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }

    /**
     * Get the password attribute (decrypt it).
     *
     * @param  string  $value
     * @return string
     */
    public function getPasswordAttribute($value)
    {
        return decrypt($value);
    }
}
