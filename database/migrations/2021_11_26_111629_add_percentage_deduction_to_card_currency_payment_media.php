<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPercentageDeductionToCardCurrencyPaymentMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_currency_payment_media', function (Blueprint $table) {
            $table->integer('percentage_deduction')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_currency_payment_media', function (Blueprint $table) {
            $table->dropColumn('percentage_deduction');
        });
    }
}
