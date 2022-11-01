<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    protected $guarded = [];
    /* public function rates()
    {
        return $this->hasMany('App\Rate', 'card', 'name');
    } */

    /**
     * Get the currencies attached to this card
     *
     * @return void
     */
    public function currency()
    {
        return $this->belongsToMany(\App\Currency::class, 'card_currencies')->withPivot(['buy_sell', 'id']);
    }

    public function cardCurrency()
    {
        return $this->hasOne('App\CardCurrency','card_id');
    }

}
