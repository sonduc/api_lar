<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostReviewCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_review_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('room_id')->nullable();
            $table->integer('booking_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('merchant_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->float('avg_rating')->nullable();
            $table->tinyInteger('cleanliness')->nullable();
            $table->tinyInteger('friendly')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('recommend')->nullable();
            $table->tinyInteger('house_rules_observe')->nullable();
            $table->integer('checkin')->nullable();
            $table->integer('checkout')->nullable();
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
        Schema::dropIfExists('host_review_customer');
    }
}
