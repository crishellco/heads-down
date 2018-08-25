<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class User extends Model
{

    protected $guarded = [];

    protected $hidden = [
        'slack_token',
    ];

    public function headsDownSessions()
    {
        return $this->hasMany(Session::class);
    }

}
