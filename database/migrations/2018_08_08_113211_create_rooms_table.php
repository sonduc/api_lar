<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->tinyInteger('max_additional_guest')->default(0);
            $table->tinyInteger('number_bed')->nullable();
            $table->tinyInteger('number_room')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('room_type')->nullable();
            $table->time('checkin')->nullable();
            $table->time('checkout')->nullable();
            $table->integer('price_day')->default(0);
            $table->integer('price_hour')->default(0);
            $table->integer('price_after_hour')->default(0);
            $table->integer('price_charge_guest')->default(0);
            $table->integer('cleaning_fee')->default(0);
            $table->tinyInteger('standard_point')->nullable();
            $table->tinyInteger('is_manager')->default(0);
            $table->tinyInteger('hot')->default(0);
            $table->tinyInteger('new')->default(0);
            $table->integer('latest_deal')->nullable();
            $table->tinyInteger('rent_type')->default(3);
            $table->text('rules')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->integer('total_booking')->nullable()->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('sale_id')->nullable();
            $table->softDeletes()->nullable()->default(NULL);
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
