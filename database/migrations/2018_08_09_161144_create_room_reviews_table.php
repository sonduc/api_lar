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
            $table->integer('booking_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->float('avg_rating')->nullable();
            $table->tinyInteger('cleanliness')->nullable();
            $table->tinyInteger('quality')->nullable();
            $table->tinyInteger('service')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('recommend')->nullable();
            $table->tinyInteger('valuable')->nullable();
            $table->tinyInteger('like')->nullable();
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
        Schema::dropIfExists('room_reviews');
    }
}
