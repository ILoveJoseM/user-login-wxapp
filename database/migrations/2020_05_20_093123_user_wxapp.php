<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserWxapp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wxapp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(false)->comment("用户ID");
            $table->string('openid',64)->nullable(false)->comment("微信开放ID");
            $table->string('unionid',64)->nullable(false)->comment("微信唯一ID");
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
        Schema::dropIfExists('user_wxapp');
    }
}
