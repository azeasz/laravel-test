<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaunaIdToSuggestionsTable extends Migration
{
    public function up()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->unsignedInteger('fauna_id')->after('user_id');
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropForeign(['fauna_id']);
            $table->dropColumn('fauna_id');
        });
    }
}
