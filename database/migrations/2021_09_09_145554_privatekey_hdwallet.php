<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivatekeyHdwallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hd_wallets', function (Blueprint $table) {
            $table->string('private_key')->after('xpub')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hd_wallets', function (Blueprint $table) {
            $table->dropColumn('private_key');
        });
    }
}
