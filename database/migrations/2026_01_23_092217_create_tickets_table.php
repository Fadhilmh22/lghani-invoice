<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('airline_id');
            $table->string('booking_code', 20);
            $table->string('class')->nullable(); // Sekarang jadi isian teks
            
            // Penerbangan PERGI (Outbound)
            $table->string('flight_out')->nullable();
            $table->string('route_out')->nullable();
            $table->dateTime('dep_time_out')->nullable();
            $table->dateTime('arr_time_out')->nullable();
        
            // Penerbangan PULANG (Inbound) - Opsional
            $table->string('flight_in')->nullable();
            $table->string('route_in')->nullable();
            $table->dateTime('dep_time_in')->nullable();
            $table->dateTime('arr_time_in')->nullable();
        
            // Rincian Harga
            $table->decimal('basic_fare', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_publish', 15, 2)->default(0); // Harga Jual
            $table->decimal('total_profit', 15, 2)->default(0);
        
            $table->timestamps();

            // Opsional: Tambahkan foreign key agar data konsisten
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};