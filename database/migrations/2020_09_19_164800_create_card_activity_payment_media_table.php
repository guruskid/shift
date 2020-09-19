<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardActivityPaymentMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_activity_payment_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('card_activity_id');
            $table->bigInteger('payment_media_id');
            $table->longText('payment_range_settings');
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
        Schema::dropIfExists('card_activity_payment_media');
    }
}
