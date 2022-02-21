<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class updateTypeOnNairaTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naira_trades', function (Blueprint $table) {
            DB::statement('UPDATE naira_trades set `type`= "withdrawal" where `type` = "sell"');
            DB::statement('UPDATE naira_trades set `type`= "deposit" where `type` = "buy"');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
