<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRating extends Model
{
   use SoftDeletes;
   protected $fillable = ['user_id', 'rate', 'text'];
   protected $hidden = ['user_id', 'created_at', 'id', 'updated_at', 'deleted_at'];
}
