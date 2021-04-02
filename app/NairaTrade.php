<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NairaTrade extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
