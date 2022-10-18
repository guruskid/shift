<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;
    protected $fillable = ["author_id", "slug", "title", "description", "body", 'published_at', "status", "blog_category_id", "blog_heading_id", 'image'];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];


    protected static function boot() {
        parent::boot();

        static::creating(function ($blog) {
            // generate a slug base on the title
          $slug =   $blog->slug = Str::slug($blog->title);

            // check to see if any other slugs exist that are the same & count them
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        // if other slugs exist that are the same, append the count to the slug
        $blog->slug = $count ? "{$slug}-{$count}" : $slug;

        });
    }

}
