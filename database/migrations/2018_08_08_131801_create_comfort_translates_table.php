<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComfortTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comfort_translates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('comfort_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('lang', 5)->nullable();
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
        Schema::dropIfExists('comfort_translates');
    }
}
