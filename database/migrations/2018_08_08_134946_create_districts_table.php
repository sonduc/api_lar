<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->nullable();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('code')->nullable();
            $table->tinyInteger('kind_from')->nullable();
            $table->tinyInteger('kind_to')->nullable();
            $table->tinyInteger('hot')->nullable();
            $table->tinyInteger('priority')->nullable();
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
        Schema::dropIfExists('districts');
    }
}
