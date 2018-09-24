<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id')->nullable();
            $table->bigInteger('money_received')->nullable()->default(0);
            $table->bigInteger('total_debt')->nullable()->default(0);
            $table->bigInteger('total_received')->nullable()->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('confirm')->nullable();
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
        Schema::dropIfExists('payment_histories');
    }
}
