<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockfillOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blockfill_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('currency_id');
            $table->integer('transaction_id');
            $table->string('order_id')->nullable();
            $table->string('type');
            $table->string('pair');
            $table->double('quantity');
            $table->double('rate');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('blockfill_orders');
    }
}
