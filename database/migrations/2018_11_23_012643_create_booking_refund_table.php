<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_refund', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('booking_id')->nullable();
            $table->integer('days')->nullable();
            $table->tinyInteger('refund')->nullable();
            $table->tinyInteger('no_booking_cancel')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('booking_refund');
    }
}
