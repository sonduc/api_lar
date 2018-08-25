<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_translates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blog_id');
            $table->string('title',100)->nullable();
            $table->string('slug',100)->nullable();
            $table->text('teaser',100)->nullable();
            $table->text('content')->nullable();
            $table->integer('lang_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_translates');
    }
}
