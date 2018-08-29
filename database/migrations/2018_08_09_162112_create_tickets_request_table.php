<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('ticket_type_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('google_id')->nullable();
            $table->tinyInteger('status')->nullable();
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
        Schema::dropIfExists('tickets_request');
    }
}
