<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMedium extends Model
{
    //

    public function currency()
    {
        return $this->belongsTo(\App\Currency::class);
    }
}
