<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
   Protected $guarded = [];

   public function user(){
      return $this->belongsTo('App\User','wiped_by');
  }
}
