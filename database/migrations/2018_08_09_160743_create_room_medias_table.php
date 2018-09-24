<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('room_id')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('room_medias');
    }
}
