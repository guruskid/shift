<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFlaggedIsDailylimitIsMonthlylimitNairaTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naira_trades', function (Blueprint $table) {
            $table->string('is_flagged')->default(0)->nullable();
            $table->string('is_dailyLimit')->default(0)->nullable();
            $table->string('is_monthlyLimit')->default(0)->nullable();

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
            $table->dropColumn(['is_flagged','is_dailyLimit','is_monthlyLimit']);
        });
    }
}
