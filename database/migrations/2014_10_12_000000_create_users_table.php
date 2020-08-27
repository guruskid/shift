<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email', 100)->unique();
            $table->string('phone')->nullable();
            $table->integer('role')->default(1);
            $table->string('id_card')->nullable();
            $table->string('dp')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', ['not verified', 'waiting', 'verified', 'declined', 'active'] )->default("not verified");
            $table->string('password');
            $table->integer('is_deleted')->default(0);
            $table->string('reff')->nullable();
            $table->string('reff_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
