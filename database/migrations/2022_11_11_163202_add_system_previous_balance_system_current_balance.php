<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSystemPreviousBalanceSystemCurrentBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naira_transactions', function (Blueprint $table) {
            $table->integer('system_previous_balance')->after('current_balance')->default(0)->nullable();
            $table->integer('system_current_balance')->after('system_previous_balance')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('naira_transactions', function (Blueprint $table) {
            $table->dropColumn(['system_previous_balance','system_current_balance']);
        });
    }
}
