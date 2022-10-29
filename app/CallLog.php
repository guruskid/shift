<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $guarded = [];
    
    public function user() {
        return $this->belongsTo('App\User','user_id');
    }
    public function call_category()
    {
        return $this->belongsTo('App\CallCategory','call_category_id');
    }



}
