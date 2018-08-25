<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('uuid',20)->nullable();
            $table->string('code',50)->nullable();
            $table->string('name',100)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email',50)->nullable();
            $table->string('email_received',50)->nullable();
            $table->string('name_received',100)->nullable();
            $table->string('phone_received',20)->nullable();
            $table->integer('room_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('merchant_id')->nullable();
            $table->integer('checkin')->nullable();
            $table->integer('checkout')->nullable();
            $table->integer('price_original')->nullable();
            $table->integer('price_discount')->nullable();
            $table->integer('booking_fee')->nullable();
            $table->string('coupon',50)->nullable();
            $table->string('note')->nullable();
            $table->integer('total_fee')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('number_of_guests')->nullable();
            $table->integer('service_fee')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('booking_type')->nullable();
            $table->tinyInteger('payment_method')->nullable();
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
