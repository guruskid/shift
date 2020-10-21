<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardCurrency extends Model
{
    protected $guarded = [];


    public function paymentMediums()
    {
        return $this->belongsToMany(\App\PaymentMedium::class, 'card_currency_payment_media')->withPivot(['payment_range_settings']);
    }

    public function card()
    {
        return $this->belongsTo('App\Card');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

}

