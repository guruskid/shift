<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpeningBalanceToAccountantTimeStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accountant_time_stamps', function (Blueprint $table) {
            $table->bigInteger('opening_balance')->default(0)->after('inactiveTime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accountant_time_stamps', function (Blueprint $table) {
            //
        });
    }
}
