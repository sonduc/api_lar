<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_translates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 5)->nullable();
            $table->integer('room_id');
            $table->string('name', 150)->nullable()->index();
            $table->string('slug_name')->nullable();
            $table->string('address')->nullable();
            $table->string('slug_address')->nullable();
            $table->text('note')->nullable();
            $table->text('space')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('room_translates');
    }
}
