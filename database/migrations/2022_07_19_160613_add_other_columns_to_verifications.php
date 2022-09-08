<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtherColumnsToVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('location')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn('id_type');
            $table->dropColumn('id_number');
            $table->dropColumn('location');

        });
    }
}
