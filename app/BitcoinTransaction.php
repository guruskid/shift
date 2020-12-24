<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitcoinTransaction extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function type()
    {
        return $this->belongsTo('App\TransactionType', 'transaction_type_id', 'id');
    }
    /* public function user()
    {
        return $this->belongsTo('App\User', 'foreign_key', 'other_key');
    } */
}
