<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'wiped_by');
    }
}
