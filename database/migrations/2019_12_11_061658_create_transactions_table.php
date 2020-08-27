<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_email', 100);
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');
            $table->string('card', 100);
            $table->string('country', 100);
            $table->integer('amount');
            $table->integer('amount_paid')->nullable();
            $table->enum('status', ['success', 'waiting', 'failed', 'declined'] )->default("waiting");
            $table->string('pop')->nullable();
            $table->string('last_edited')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
