<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecycleBinEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('recycle_bin_entries', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type', 100)->index();
            $table->unsignedBigInteger('record_id')->index();
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('related_records')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            $table->string('deleted_by_name')->nullable();
            $table->string('deleted_by_email')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recycle_bin_entries');
    }
}
