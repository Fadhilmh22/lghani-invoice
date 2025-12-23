<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->string('genre');
            $table->string('name');
            $table->string('booking_code');
            $table->unsignedInteger('airline_id');
            $table->integer('airlines_no');
            $table->string('class');
            $table->string('ticket_no');
            $table->string('route');
            $table->date('depart_date');
            $table->date('return_date');
            $table->integer('pax_paid');
            $table->integer('price');
            $table->integer('discount');
            $table->integer('nta');
            $table->integer('profit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
}
