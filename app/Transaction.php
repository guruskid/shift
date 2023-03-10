<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $casts = [
        'commission' => 'double',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function agent()
    {
        return $this->belongsTo('App\User', 'agent_id', 'id');
    }

    public function accountant()
    {
        return $this->belongsTo('App\User', 'accountant_id', 'id');
    }

    public function pops()
    {
        return $this->hasMany('App\Pop')->latest();
    }

    public function batchPops()
    {
        return $this->hasMany('App\Pop', 'transaction_id', 'batch_id')->latest();
    }

    public function asset()
    {
        return $this->belongsTo('App\Card', 'card_id', 'id');
    }

    public function naira_transactions(){
        return $this->belongsTo(NairaTransaction::class,'naira_transaction_id','id');
    }





}
