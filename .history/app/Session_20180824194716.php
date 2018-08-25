<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

    protected $guarded = [];

    protected $dates = ['started_at', 'ended_at'];

}
