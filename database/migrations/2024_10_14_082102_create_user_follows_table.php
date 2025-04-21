<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowsTable extends Migration
{
    public function up()
    {
        Schema::create('user_follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id');
            $table->unsignedBigInteger('followed_id');
            $table->timestamps();

            $table->foreign('follower_id')->references('id')->on('fobi_users')->onDelete('cascade');
            $table->foreign('followed_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_follows');
    }
}
