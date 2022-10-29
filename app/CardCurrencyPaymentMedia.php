<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardCurrencyPaymentMedia extends Model
{
    //

    protected $guarded = [];

    public function cardCurrency()
    {
        return $this->belongsTo('App\CardCurrency');
    }

    public function paymentMedium()
    {
        return $this->belongsTo('App\PaymentMedium');
    }

}
