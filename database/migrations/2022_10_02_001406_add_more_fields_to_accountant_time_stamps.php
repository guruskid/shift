<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToAccountantTimeStamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accountant_time_stamps', function (Blueprint $table) {
            //
            $table->string('activated_by')->nullable();
            $table->string('deactivated_by')->nullable();
            $table->dateTime('activation_date')->nullable();
            $table->dateTime('deactivation_date')->nullable();
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
            $table->dropColumn('activated_by');
            $table->dropColumn('deactivated_by');
            $table->dropColumn('activation_date');
            $table->dropColumn('deactivation_date');
        });
    }
}
