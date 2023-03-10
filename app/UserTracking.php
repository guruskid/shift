<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTracking extends Model
{
    protected $guarded = [];
    
    public function user() {
        return $this->belongsTo('App\User','user_id');
    }
    
    public function transactions()
    {
        return $this->hasMany('App\Transaction','user_id','user_id')->where('status','success')->latest();
    }
    
    public function call_log()
    {
        return $this->belongsTo('App\CallLog','call_log_id')->latest();
    }
}
