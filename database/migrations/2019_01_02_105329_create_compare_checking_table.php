<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompareCheckingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compare_checking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('total_credit')->nullable();
            $table->integer('total_debit')->nullable();
            $table->integer('total_bonus')->nullable();
            $table->integer('total_compare_checking')->nullable();
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
        Schema::dropIfExists('compare_checking');
    }
}
