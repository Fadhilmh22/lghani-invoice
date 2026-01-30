<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceColumnsToBigint extends Migration
{
    public function up()
    {
        // Update Tabel Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->bigInteger('total')->default(0)->change();
        });

        // Update Tabel Tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->bigInteger('basic_fare')->default(0)->change();
            $table->bigInteger('total_tax')->default(0)->change();
            $table->bigInteger('fee')->default(0)->change();
            $table->bigInteger('baggage_price')->default(0)->change();
            $table->bigInteger('total_publish')->default(0)->change();
            $table->bigInteger('total_profit')->default(0)->change();
        });

        // Update Tabel Invoice_details
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->bigInteger('price')->default(0)->change();
            $table->bigInteger('discount')->default(0)->change();
            $table->bigInteger('pax_paid')->default(0)->change();
            $table->bigInteger('nta')->default(0)->change();
            $table->bigInteger('profit')->default(0)->change();
        });
    }

    public function down()
    {
        // Jika ingin dikembalikan ke decimal (opsional)
    }
}