<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePhoneColumnTypeInHotelsTable extends Migration
{
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Kita ubah ke string/varchar tanpa limit 13 lagi
            // nullable() supaya kalau dikosongkan tidak error
            $table->string('phone')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Kembalikan ke format lama jika di-rollback (opsional)
            $table->string('phone', 13)->change();
        });
    }
}