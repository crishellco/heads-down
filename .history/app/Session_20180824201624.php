<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

    protected $dates = ['started_at', 'ended_at'];

    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class);
    }

}
