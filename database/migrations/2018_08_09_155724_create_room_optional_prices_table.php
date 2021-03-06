<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('price_day')->nullable()->default(0);
            $table->integer('price_hour')->nullable()->default(0);
            $table->integer('price_after_hour')->nullable()->default(0);
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
