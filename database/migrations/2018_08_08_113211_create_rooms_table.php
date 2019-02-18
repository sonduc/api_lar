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
            $table->integer('price_day')->nullable()->default(0);
            $table->integer('price_hour')->nullable()->default(0);
            $table->integer('price_after_hour')->nullable()->default(0);
            $table->integer('price_charge_guest')->nullable()->default(0);
            $table->integer('cleaning_fee')->nullable()->default(0);
            $table->tinyInteger('standard_point')->nullable();
            $table->tinyInteger('is_manager')->default(0);
            $table->tinyInteger('hot')->default(0);
            $table->tinyInteger('new')->default(0);
            $table->tinyInteger('latest_deal')->default(0);
            $table->tinyInteger('rent_type')->default(3);
            $table->text('rules')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->integer('total_booking')->nullable()->default(0);
            $table->tinyInteger('avg_cleanliness')->nullable()->default(0);
            $table->tinyInteger('avg_quality')->nullable()->default(0);
            $table->tinyInteger('avg_service')->nullable()->default(0);
            $table->tinyInteger('avg_valuable')->nullable()->default(0);
            $table->tinyInteger('avg_avg_rating')->nullable()->default(0);
            $table->integer('total_review')->nullable()->default(0);
            $table->integer('total_recommend')->nullable()->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('sale_id')->nullable();
            $table->integer('percent')->default(0);
            $table->integer('comission')->default(20);
            $table->longText('settings')->nullable();
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
