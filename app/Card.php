<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public function rates()
    {
        return $this->hasMany('App\Rate', 'card', 'name');
    }
}
