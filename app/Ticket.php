<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = []; 

    public function subcategories()
    {
        return $this->belongsTo(TicketCategory::class, 'subcategory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
