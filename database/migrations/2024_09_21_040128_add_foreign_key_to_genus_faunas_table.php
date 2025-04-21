<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToGenusFaunasTable extends Migration
{
    public function up()
    {
        Schema::table('genus_faunas', function (Blueprint $table) {
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('genus_faunas', function (Blueprint $table) {
            $table->dropForeign(['fauna_id']);
        });
    }
}
