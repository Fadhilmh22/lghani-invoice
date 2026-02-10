<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Stop details untuk penerbangan PERGI (Outbound)
            $table->string('stop_flight_leg1_out')->nullable()->comment('Flight number dari departure ke stop');
            $table->dateTime('stop_time_out')->nullable()->comment('Waktu arrival/departure di stop (legacy)');
            $table->dateTime('stop_time_out_arrival')->nullable()->comment('Waktu arrival di stop (outbound)');
            $table->dateTime('stop_time_out_depart')->nullable()->comment('Waktu departure dari stop (outbound)');
            $table->string('stop_flight_leg2_out')->nullable()->comment('Flight number dari stop ke arrival');

            // Stop details untuk penerbangan PULANG (Inbound)
            $table->string('stop_flight_leg1_in')->nullable()->comment('Flight number dari departure ke stop');
            $table->dateTime('stop_time_in')->nullable()->comment('Waktu arrival/departure di stop (legacy)');
            $table->dateTime('stop_time_in_arrival')->nullable()->comment('Waktu arrival di stop (inbound)');
            $table->dateTime('stop_time_in_depart')->nullable()->comment('Waktu departure dari stop (inbound)');
            $table->string('stop_flight_leg2_in')->nullable()->comment('Flight number dari stop ke arrival');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'stop_flight_leg1_out',
                'stop_time_out',
                'stop_time_out_arrival',
                'stop_time_out_depart',
                'stop_flight_leg2_out',
                'stop_flight_leg1_in',
                'stop_time_in',
                'stop_time_in_arrival',
                'stop_time_in_depart',
                'stop_flight_leg2_in'
            ]);
        });
    }
};
