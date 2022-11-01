<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlaggedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flagged_transactions', function (Blueprint $table) {
            $table->timestamps();
            $table->bigIncrements('id');
            $table->string('type');
            $table->bigInteger('user_id');
            $table->bigInteger('transaction_id');
            $table->string('reference_id');
            $table->integer('previousTransactionAmount');
            $table->bigInteger('accountant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flagged_transactions');
    }
}
