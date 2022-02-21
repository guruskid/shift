<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerificationLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_limits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('level')->unique();
            $table->integer('daily_widthdrawal_limit');
            $table->integer('monthly_widthdrawal_limit');
            $table->string('crypto_widthdrawal_limit');
            $table->string('crypto_deposit');
            $table->string('transactions');
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
        Schema::dropIfExists('verification_limits');
    }
}
