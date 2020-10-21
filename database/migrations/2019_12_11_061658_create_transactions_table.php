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
            $table->string('uid');
            $table->string('user_email', 100);
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');
            $table->bigInteger('user_id');
            $table->string('card', 100);
            $table->bigInteger('card_id');
            $table->string('country', 100);
            $table->string('type');
            $table->integer('amount');
            $table->integer('amount_paid')->nullable();
            $table->bigInteger('agent_id');
            $table->bigInteger('accountant_id');
            $table->enum('status', ['success', 'waiting', 'failed', 'declined'] )->default("waiting");
            $table->string('wallet_id');
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
