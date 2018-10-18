<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_rooms', function (Blueprint $table) {
            $table->bigInteger('room_id');
            $table->bigInteger('collection_id');
            $table->primary(['room_id', 'collection_id']);
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
        Schema::dropIfExists('collection_rooms');
    }
}
