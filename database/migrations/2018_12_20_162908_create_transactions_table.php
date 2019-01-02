<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->nullable();
            $table->integer('credit')->nullable()->default(0);
            $table->integer('debit')->nullable()->default(0);
            $table->string('date_create')->nullable();
            $table->integer('room_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('booking_id')->nullable();
            $table->integer('bonus')->nullable()->default(0);
            $table->integer('comission')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
