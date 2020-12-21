<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBitcoinTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitcoin_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('primary_wallet_id')->nullable();
            $table->string('wallet_id');
            $table->string('hash');
            $table->double('credit')->nullable();
            $table->double('debit')->nullable();
            $table->double('fee');
            $table->double('charge');
            $table->double('previous_balance');
            $table->double('current_balance');
            $table->bigInteger('transaction_type_id');
            $table->string('counterparty');
            $table->string('narration');
            $table->bigInteger('confirmations');
            $table->enum('status', ['success', 'pending', 'failed'])->nullable();
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
        Schema::dropIfExists('bitcoin_transactions');
    }
}
