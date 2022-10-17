<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogHeading extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', "is_published"];
    protected $hidden = ["created_at", "updated_at"];
}
