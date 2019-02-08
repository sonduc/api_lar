<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDiscountRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('is_discount')->after('latest_deal')->nullable()->default(0);
            $table->integer('price_day_discount')->after('is_discount')->nullable();
            $table->integer('price_hour_discount')->after('price_day_discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('is_discount');
            $table->dropColumn('price_day_discount');
            $table->dropColumn('price_hour_discount');
        });
    }
}
