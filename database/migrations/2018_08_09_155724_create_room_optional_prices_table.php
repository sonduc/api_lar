<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomOptionalPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_optional_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('room_id')->nullable();
            $table->tinyInteger('weekday')->nullable();
            $table->date('day')->nullable();
            $table->integer('price_day')->nullable();
            $table->integer('price_hour')->nullable();
            $table->integer('price_after_hour')->nullable();
            $table->integer('price_charge_guest')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
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
        Schema::dropIfExists('room_optional_prices');
    }
}
