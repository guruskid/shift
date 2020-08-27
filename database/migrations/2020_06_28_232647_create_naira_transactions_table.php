<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNairaTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('naira_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference');
            $table->integer('amount');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('type', ['naira wallet', 'bank transfer', 'paytv', 'mobile data', 'recharge card', 'elecriciy bills' ] );
            $table->bigInteger('cr_user_id')->nullable();
            $table->bigInteger('dr_user_id')->nullable();
            $table->bigInteger('dr_wallet_id')->nullable();
            $table->string('dr_acct_name')->nullable();
            $table->bigInteger('cr_wallet_id')->nullable();
            $table->string('cr_acct_name')->nullable();
            $table->string('narration');
            $table->string('trans_msg', 3000 );
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
        Schema::dropIfExists('naira_transactions');
    }
}
