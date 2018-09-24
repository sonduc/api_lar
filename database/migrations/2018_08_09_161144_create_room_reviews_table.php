<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('room_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('rate')->nullable();
            $table->tinyInteger('status')->default(-1);
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
        Schema::dropIfExists('room_reviews');
    }
}
