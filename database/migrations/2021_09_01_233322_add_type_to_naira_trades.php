<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToNairaTrades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naira_trades', function (Blueprint $table) {
            $table->enum('type', ['sell', 'buy'] )->default("buy");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('naira_trades', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
