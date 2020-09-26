<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardActivityPaymentMedium extends Model
{
    //
    protected $guarded = [];

    
    /**
     * Retrieves the card activity for this model
     *
     * @return void
     */
    public function cardActivity()
    {
        return $this->belongsTo(\App\CardActivity::class, 'card_activity_id');
    }

    
    /**
     * Retreives the payment medium for this model
     *
     * @return void
     */
    public function paymentMedium()
    {
        return $this->belongsTo(\App\PaymentMedium::class);
    }
}
