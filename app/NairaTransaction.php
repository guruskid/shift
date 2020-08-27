<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NairaTransaction extends Model
{
    public function transactionType()
    {
        return $this->belongsTo('App\TransactionType');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
