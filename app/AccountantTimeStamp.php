<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountantTimeStamp extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function activatedBy(){
        return $this->belongsTo('App\User',  'activated_by', 'id');
    }
}
