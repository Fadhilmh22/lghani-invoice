<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dateTime('stop_time_out_arrival2')->nullable();
            $table->dateTime('stop_time_out_depart2')->nullable();
            $table->dateTime('stop_time_in_arrival2')->nullable();
            $table->dateTime('stop_time_in_depart2')->nullable();
            $table->string('stop_flight_leg3_out')->nullable();
            $table->string('stop_flight_leg3_in')->nullable();
            $table->string('stop_airline_leg3_out')->nullable();
            $table->string('stop_airline_leg3_in')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'stop_time_out_arrival2',
                'stop_time_out_depart2',
                'stop_time_in_arrival2',
                'stop_time_in_depart2',
                'stop_flight_leg3_out',
                'stop_flight_leg3_in',
                'stop_airline_leg3_out',
                'stop_airline_leg3_in',
            ]);
        });
    }
};
