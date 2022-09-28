<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNairaWalletsAddWithheldFunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naira_wallets', function (Blueprint $table) {
            $table->integer('withheld_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('naira_wallets', function (Blueprint $table) {
            $table->dropColumn('withheld_amount');
        });
    }
}
