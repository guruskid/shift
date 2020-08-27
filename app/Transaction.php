<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_email', 'email');
    }

    public function agent()
    {
        return $this->belongsTo('App\User', 'agent_id', 'id');
    }

    public function pops()
    {
        return $this->hasMany('App\Pop')->orderBy('created_at', 'desc');
    }

    public function asset()
    {
        return $this->belongsTo('App\Card', 'card_id', 'id');
    }


}
