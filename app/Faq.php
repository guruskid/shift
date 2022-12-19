<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->hasOne(FaqCategory::class, 'id', 'category_id');
    }
}
