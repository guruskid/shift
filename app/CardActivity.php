<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardActivity extends Model
{
    //


    protected $guarded = [];


    public function card()
    {
        return $this->belongsTo(\App\Card::class);
    }
}
