<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteTaxaTable extends Migration
{
    public function up()
    {
        Schema::create('favorite_taxa', function (Blueprint $table) {
            $table->id();
            $table->string('taxa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_taxa');
    }
}
