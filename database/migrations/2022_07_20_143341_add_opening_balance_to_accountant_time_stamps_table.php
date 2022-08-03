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
            $table->bigInteger('opening_balance')->nullable()->after('inactiveTime');
            $table->bigInteger('closing_balance')->nullable()->after('opening_balance');
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
            $table->dropColumn('opening_balance','closing_balance');
        });
    }
}
