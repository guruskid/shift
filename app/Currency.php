<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //

    protected $guarded = [];

    
    /**
     * Returns an hasmany relationship that we can use to retrieve the real payment medias
     *
     * @return void
     */
    public function paymentMedia()
    {
        return $this->hasMany(\App\CurrencyPaymentMedium::class);
    }
}
