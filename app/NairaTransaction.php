<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NairaTransaction extends Model
{
    protected $guarded = [];

    public function transactionType()
    {
        return $this->belongsTo('App\TransactionType');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function utility()
    {
        return $this->hasOne('App\UtilityTransaction', 'reference_id', 'reference');
    }
}
