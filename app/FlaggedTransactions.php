<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlaggedTransactions extends Model
{
    protected $guarded = [];

    public function naira_transaction()
    {
        return $this->belongsTo(NairaTransaction::class,'reference_id','reference');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function nairaTrade()
    {
        return $this->belongsTo(NairaTrade::class,'transaction_id','id');
    }

    public function accountant()
    {
        return $this->belongsTo(User::class,'accountant_id','id');
    }

}
