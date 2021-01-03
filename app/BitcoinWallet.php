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

    public function secondaryWallets()
    {
        return $this->hasMany('App\BitcoinWallet', 'primary_wallet_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany('App\BitcoinTransaction', 'wallet_id', 'address')->latest();
    }
}
