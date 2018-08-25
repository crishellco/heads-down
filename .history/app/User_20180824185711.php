<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $guarded = [];

    protected $hidden = [
        'slack_token',
    ];

    public function currentSession()
    {
        return $this->hasMany(Session::class)->whereNull('created_at')->latest()->take(1);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

}
