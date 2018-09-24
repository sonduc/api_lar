<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRoomCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('room_categories', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name')->nullable();
        //     $table->string('display_name')->nullable();
        //     $table->tinyInteger('status')->nullable();
        //     $table->softDeletes()->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_categories');
    }
}
