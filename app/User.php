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
        return $this->hasOne(Session::class)->whereNull('ended_at')->latest();
    }

    public function hasCurrentSession()
    {
        return !!$this-currentSession;
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

}
