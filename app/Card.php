<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public function rates()
    {
        return $this->hasMany('App\Rate', 'card', 'name');
    }

    public function activity()
    {
        return $this->hasMany(\App\CardActivity::class);
    }

    public function activityPaymentMedium()
    {
        return $this->hasManyThrough(\App\PaymentMedium::class);
    }


}
