<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $guarded = [];

    protected $hidden = [
        'slack_token',
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

}
