<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrentCycleCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->timestamp('current_cycle_count_date')->nullable()->after('Current_Cycle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_trackings', function (Blueprint $table) {
            $table->dropColumn('current_cycle_count_date');
        });
    }
}
