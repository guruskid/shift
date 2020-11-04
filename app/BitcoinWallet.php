<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitcoinWallet extends Model
{
    protected $guarded = ['balance'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function primaryWallet()
    {
        return $this->belongsTo('App\BitcoinWallet', 'primary_wallet_id');
    }
}
