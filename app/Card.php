<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public function rates()
    {
        return $this->hasMany('App\Rate', 'card', 'name');
    }

    public function activity()
    {
        return $this->hasMany(\App\CardActivity::class);
    }
    
    // /**
    //  * Returns hasmany relationship of activity payment mediums we can use to retrieve 
    //  * The actual payment mediums
    //  *
    //  * @return void
    //  */
    // public function activityPaymentMedium()
    // {
    //     return $this->hasManyThrough(\App\CardActivityPaymentMedium::class);
    // }


}
