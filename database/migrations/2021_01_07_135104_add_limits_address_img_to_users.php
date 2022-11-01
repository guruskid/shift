<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLimitsAddressImgToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_img')->nullable();
            $table->integer('daily_max')->nullable();
            $table->integer('monthly_max')->nullable();
            $table->integer('v_progress')->nullable();
            $table->timestamp('address_verified_at')->nullable();
            $table->timestamp('idcard_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address_img', 'daily_max', 'monthly_max', 'v_progress']);
        });
    }
}
