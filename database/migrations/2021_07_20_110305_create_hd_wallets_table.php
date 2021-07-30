<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHdWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hd_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('currency_id');
            $table->string('name');
            $table->string('signature_id');
            $table->longText('xpub');
            $table->string('address')->nullable();
            $table->string('account_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('pin')->nullable();
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
        Schema::dropIfExists('hd_wallets');
    }
}
