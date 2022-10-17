<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('author_id')
            ->foreign('author_id')
            ->references('id')
            ->on('users');
            $table->bigInteger('blog_category_id')
            ->foreign('blog_category_id')
            ->references('id')
            ->on('blog_categories');
            $table->string('slug', 255)->unique()->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->enum('status', ['draft', 'published', 'outdated']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
