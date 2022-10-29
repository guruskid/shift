<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingCallLogCallResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            DB::statement('ALTER TABLE call_logs MODIFY COLUMN `call_response` LONGTEXT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            DB::statement('ALTER TABLE call_logs MODIFY COLUMN `call_response` VARCHAR(191)');
        });
    }
}
