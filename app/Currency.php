<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //

    protected $guarded = [];


    public function paymentMedia()
    {
        return $this->hasMany(\App\PaymentMedium::class);
    }
}
