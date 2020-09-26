<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardActivity extends Model
{
    //


    protected $guarded = [];


    public function card()
    {
        return $this->belongsTo(\App\Card::class);
    }
    
    /**
     * This will return the has many relationship through which we fetch each payment medium
     *
     * @return void
     */
    public function paymentMedia()
    {
        return $this->hasMany(\App\CardActivityPaymentMedium::class);
    }
}
