<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('stop_airline_out')->nullable()->after('stop_flight_leg2_out')
                  ->comment('Maskapai pada leg stop outbound jika berbeda');
            $table->string('stop_airline_in')->nullable()->after('stop_flight_leg2_in')
                  ->comment('Maskapai pada leg stop inbound jika berbeda');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['stop_airline_out', 'stop_airline_in']);
        });
    }
};
