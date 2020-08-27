<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('card', 100);
            $table->foreign('card')->references('name')->on('cards')->onDelete('cascade');
            $table->integer('usd')->nullable();
            $table->integer('eur')->nullable();
            $table->integer('gbp')->nullable();
            $table->integer('aud')->nullable();
            $table->integer('cad')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('rates');
    }
}
