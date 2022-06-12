<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallDurationToNewUsersTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_users_trackings', function (Blueprint $table) {
            $table->string('call_duration')->nullable();
            $table->string('call_duration_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_users_trackings', function (Blueprint $table) {
            $table->dropColumn('call_duration');
            $table->dropColumn('call_duration_timestamp');
        });
    }
}
