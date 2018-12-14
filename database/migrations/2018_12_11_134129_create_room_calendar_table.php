<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_calendar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamp('starts')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('ends')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status')->nullable();
            $table->text('summary')->nullable();
            $table->string('location')->nullable();
            $table->string('uid')->nullable();
            $table->string('room_id')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('room_calendar');
    }
}
