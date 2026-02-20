<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlineTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This table records both positive top-ups and negative adjustments
     * (e.g. NTA deductions when tickets are issued or adjustments on
     * editing/cancellation). A negative `amount` indicates money taken.
     *
     * Columns `before_balance` and `after_balance` capture the state so
     * we can audit changes later without re-computing.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airline_topups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airline_id');
            $table->bigInteger('amount');
            $table->bigInteger('before_balance');
            $table->bigInteger('after_balance');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('airline_id')->references('id')->on('airlines')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airline_topups');
    }
}
