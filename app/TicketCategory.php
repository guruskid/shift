<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $guarded = [];

    public function subcategories()
    {
        return $this->hasMany(TicketCategory::class);
    }
}
