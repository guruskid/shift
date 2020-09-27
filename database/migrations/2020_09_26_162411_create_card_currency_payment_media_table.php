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
            $table->bigInteger('card_currency_id');
            $table->bigInteger('payment_medium_id');
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
