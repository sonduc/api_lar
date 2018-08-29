<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('merchant_id')->nullable();
            $table->tinyInteger('max_guest')->nullable();
            $table->tinyInteger('max_additional_guest')->nullable();
            $table->tinyInteger('number_bed')->nullable();
            $table->tinyInteger('number_room')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('room_type_id')->nullable();
            $table->time('checkin')->nullable();
            $table->time('checkout')->nullable();
            $table->integer('price_day')->nullable();
            $table->integer('price_hour')->nullable();
            $table->integer('price_after_hour')->nullable();
            $table->integer('price_charge_guest')->nullable();
            $table->integer('cleaning_fee')->nullable();
            $table->tinyInteger('standard_point')->nullable();
            $table->tinyInteger('is_manager')->nullable();
            $table->tinyInteger('hot')->nullable();
            $table->tinyInteger('new')->nullable();
            $table->tinyInteger('latest_deal')->nullable();
            $table->tinyInteger('rent_type')->nullable()->default(3);
            $table->text('rules')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->integer('total_booking')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->integer('sale_id')->nullable();
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
        Schema::dropIfExists('rooms');
    }
}
