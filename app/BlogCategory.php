<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['title', "is_published", "slug"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    protected static function boot() {
        parent::boot();

        static::creating(function ($category) {
            // generate a slug base on the title
          $slug = $category->slug = Str::slug($category->title);

            // check to see if any other slugs exist that are the same & count them
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        // if other slugs exist that are the same, append the count to the slug
        $category->slug = $count ? "{$slug}-{$count}" : $slug;

        });
    }
}
