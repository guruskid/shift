<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UtilityTransaction extends Model
{

    protected $guarded = [];


    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function nairaWallet()
    {
        return $this->belongsTo('App\NairaWallet', 'user_id');
    }
    public function bitcoinWallet()
        {
            return $this->belongsTo('App\BitcoinWallet', 'user_id');
        }
}
