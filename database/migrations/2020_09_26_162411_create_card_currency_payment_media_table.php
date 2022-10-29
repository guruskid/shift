<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardCurrencyPaymentMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_currency_payment_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('card_currency_id')->unsigned();
            $table->foreign('card_currency_id')->references('id')->on('card_currencies')->onDelete('cascade');
            $table->bigInteger('payment_medium_id')->unsigned();
            $table->foreign('payment_medium_id')->references('id')->on('payment_media')->onDelete('cascade');
            $table->longtext('payment_range_settings');
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
        Schema::dropIfExists('card_currency_payment_media');
    }
}
