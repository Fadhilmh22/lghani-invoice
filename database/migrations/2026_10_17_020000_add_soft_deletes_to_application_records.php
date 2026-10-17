<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToApplicationRecords extends Migration
{
    private $tables = [
        'tickets',
        'invoices',
        'invoice_details',
        'customers',
        'passengers',
        'hotels',
        'hotel_rates',
        'rooms',
        'hotel_invoices',
        'hotel_vouchers',
        'hotel_voucher_rooms',
        'hotel_voucher_guests',
        'airlines',
        'airports',
        'airline_topups',
    ];

    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
}
