<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyPaymentMedium extends Model
{
    //
    protected $guarded = [];

    
    /**
     * Gets the currency involved in this hasmany relationship
     *
     * @return void
     */
    public function currency()
    {
        return $this->belongsTo(\App\Currency::class);
    }

    
    /**
     * Fetches the payment medium involved in this hasmany relationship
     *
     * @return void
     */
    public function paymentMedium()
    {
        return $this->belongsTo(\App\PaymentMedium::class);
    }
}
