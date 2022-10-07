<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NairaTrade extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class,'agent_id','id');
    }

    /**
     * Get all of the pops for the NairaTrade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pops()
    {
        return $this->hasMany(NairaTradePop::class, 'transaction_id', 'id');
    }

    public function naira_transactions()
    {
        return $this->belongsTo(NairaTransaction::class,'reference','reference');
    }

    public function naria_transaction()
    {
        return $this->belongsTo(NairaTransaction::class,'reference','reference');
    }

    public function nairaWallet()
    {
        return $this->belongsTo('App\NairaWallet', 'user_id');
    }
    public function bitcoinWallet()
    {
        return $this->belongsTo('App\BitcoinWallet', 'user_id');
    }
    public function account(){
        return $this->belongsTo('App\Account', 'account_id');
    }

}
