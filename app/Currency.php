<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //

    protected $guarded = [];

    
    /**
     * Route Binding Property
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    
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
