<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceToAirlines extends Migration
{
    /**
     * Run the migrations.
     *
     * We'll add a `balance` column to store current available funds for each airline.
     * It's stored as a big integer (amount in smallest currency unit, e.g. IDR).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airlines', function (Blueprint $table) {
            // default to 0 so existing rows are not null
            $table->bigInteger('balance')->default(0)->after('airlines_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airlines', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
}
