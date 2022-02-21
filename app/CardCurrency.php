<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardCurrency extends Model
{
    protected $guarded = [];


    public function paymentMediums()
    {
        return $this->belongsToMany(\App\PaymentMedium::class, 'card_currency_payment_media')->withPivot(['payment_range_settings','percentage_deduction']);
    }

    public function card()
    {
        return $this->belongsTo('App\Card');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function cardPaymentMedia()
    {
        return $this->hasMany('\App\CardCurrencyPaymentMedium','card_currency_id');
    }

}

