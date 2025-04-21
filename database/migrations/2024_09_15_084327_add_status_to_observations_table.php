<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToObservationsTable extends Migration
{
    public function up()
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->string('status')->default('id_kurang')->after('description');
        });
    }

    public function down()
    {
        Schema::table('observations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
