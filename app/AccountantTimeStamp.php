<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountantTimeStamp extends Model
{
    protected $guarded = [];

<<<<<<< HEAD
    public function user() {
        return $this->belongsTo('App\User','user_id');
=======
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
>>>>>>> 31cf241bc84e2c5fcbd45f891dfb9865d2d405eb
    }
}
