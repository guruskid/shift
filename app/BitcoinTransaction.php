<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitcoinTransaction extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
