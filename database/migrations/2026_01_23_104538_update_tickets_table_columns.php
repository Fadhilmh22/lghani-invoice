<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketsTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'fee')) {
                $table->integer('fee')->default(0)->after('total_tax');
            }
            if (!Schema::hasColumn('tickets', 'basic_fare')) {
                $table->integer('basic_fare')->default(0)->after('class');
            }
            // Tambahkan kolom lain yang mungkin belum ada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
