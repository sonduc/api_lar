<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 20)->nullable();
            $table->string('name')->nullable();
            $table->string('email', 100)->unique();
            $table->string('password')->nullable();
            $table->tinyInteger('gender')->nullable()->default(0);
            $table->string('phone', 30)->nullable();
            $table->date('birthday')->nullable();
            $table->string('sub_email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->integer('owner')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('google_id')->nullable();
            $table->tinyInteger('level')->unsigned()->default(0);
            $table->integer('point')->unsigned()->default(0);
            $table->integer('money')->unsigned()->default(0);
            $table->string('passport_last_name')->nullable();
            $table->string('passport_first_name')->nullable();
            $table->string('passport_infomation')->nullable();
            $table->string('passport_front_card')->nullable();
            $table->string('passport_back_card')->nullable();
            $table->string('account_number')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('type')->nullable();
            $table->integer('provider_id')->nullable();
            $table->tinyInteger('vip')->nullable();
            $table->tinyInteger('is_confirm')->nullable();
            $table->tinyInteger('is_cash')->nullable();
            $table->tinyInteger('is_transfer')->nullable();
            $table->tinyInteger('is_baokim')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->longText('source')->nullable();
            $table->integer('sale_id')->default(0);
            $table->timestamp('time_add_sale')->nullable();
            $table->rememberToken()->nullable();
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
        Schema::dropIfExists('users');
    }
}
