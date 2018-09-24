<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 20)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email', 50)->nullable();
            $table->string('email_received', 50)->nullable();
            $table->string('name_received', 100)->nullable();
            $table->string('phone_received', 20)->nullable();
            $table->integer('room_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('merchant_id')->nullable();
            $table->integer('checkin')->nullable();
            $table->integer('checkout')->nullable();
            $table->bigInteger('price_original')->nullable();
            $table->bigInteger('service_fee')->nullable();
            $table->bigInteger('price_discount')->nullable();
            $table->bigInteger('booking_fee')->nullable();
            $table->string('coupon', 50)->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('total_fee')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('number_of_guests')->nullable();
            $table->tinyInteger('price_range')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('booking_type')->nullable();
            $table->tinyInteger('payment_method')->nullable();
            $table->tinyInteger('payment_status')->nullable();
            $table->tinyInteger('source')->nullable();
            $table->double('exchange_rate')->nullable();
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
        Schema::dropIfExists('bookings');
    }
}
