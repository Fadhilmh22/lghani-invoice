<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            // Ubah angka 100 sesuai dengan kebutuhan maksimal karakter kamu
            $table->string('airlines_no', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            // Ubah angka 20 dengan batas karakter atau tipe data sebelum kamu ubah
            $table->string('airlines_no', 20)->change();
        });
    }
};