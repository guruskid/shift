<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('card_id');
            $table->bigInteger('currency_id');
            $table->integer('buy_sell')->comment('1 - Buying data, 2 - selling data');
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
        Schema::dropIfExists('card_currencies');
    }
}
