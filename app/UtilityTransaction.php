<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityTransaction extends Model
{

    protected $guarded = [];


    public function user() {
        return $this->belongsTo('App\User','user_id');
    }
}
