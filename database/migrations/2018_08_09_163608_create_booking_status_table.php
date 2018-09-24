<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('staff_id')->nullable();
            $table->integer('booking_id')->nullable();
            // $table->tinyInteger('status')->nullable();
            $table->text('note')->nullable();
            $table->softDeletes()->nullable();
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
        Schema::dropIfExists('bookings_status');
    }
}
