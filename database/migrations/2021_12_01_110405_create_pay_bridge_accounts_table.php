<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayBridgeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_bridge_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->enum('account_type', ['deposit', 'withdrawal']);
            $table->enum('status', ['active', 'in-active']);
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
        Schema::dropIfExists('pay_bridge_accounts');
    }
}
