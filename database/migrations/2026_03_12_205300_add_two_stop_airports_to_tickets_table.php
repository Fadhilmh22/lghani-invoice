<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoStopAirportsToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('stop_out_depart_code')->nullable()->after('route_in')
                ->comment('Bandara kedatangan leg 1 outbound (transit pertama)');
            $table->string('stop_out_arrival_code')->nullable()->after('stop_out_depart_code')
                ->comment('Bandara keberangkatan leg 2 outbound (transit kedua)');
            $table->string('stop_in_depart_code')->nullable()->after('stop_out_arrival_code')
                ->comment('Bandara kedatangan leg 1 inbound (transit pertama pulang)');
            $table->string('stop_in_arrival_code')->nullable()->after('stop_in_depart_code')
                ->comment('Bandara keberangkatan leg 2 inbound (transit kedua pulang)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'stop_out_depart_code',
                'stop_out_arrival_code', 
                'stop_in_depart_code',
                'stop_in_arrival_code'
            ]);
        });
    }
}
