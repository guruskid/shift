<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;
    protected $fillable = ["author_id", "slug", "title", "description", "body", 'published_at', "status"];
    protected $hidden = ["created_at", "updated_at"];
}
