<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $guarded = [];
    protected $appends = ['posted'];
    protected $hidden = ['user','posted_by'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class,'posted_by','id');
    }

    public function getImageAttribute($image) {
        return url('storage/announcement/'.$image);
    }

    public function getPostedAttribute($posted_by) {
        $roles = [
            '999' => 'CEO',
            '998' => 'COO',
            '666' => 'Manager',
            '559' => 'Marketing Lead'
        ];
        return $roles[$this->user->role];
    }
}
