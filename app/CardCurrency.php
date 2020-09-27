<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardCurrency extends Model
{
    //

    
    public function paymentMediums()
    {
        return $this->belongsToMany(\App\PaymentMedium::class, 'card_currency_payment_media')->withPivot(['payment_range_settings']);
    }

    
}

