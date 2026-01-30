<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreeBaggageToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Tambahkan kolom free_baggage setelah baggage_price
            $table->integer('free_baggage')->default(0)->after('baggage_price');
        });
    }
    
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('free_baggage');
        });
    }
}
