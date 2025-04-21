<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaunaIdToGenusFaunasTable extends Migration
{
    public function up()
    {
        Schema::table('genus_faunas', function (Blueprint $table) {
            $table->unsignedBigInteger('fauna_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('genus_faunas', function (Blueprint $table) {
            $table->dropColumn('fauna_id');
        });
    }
}
