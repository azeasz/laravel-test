<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaunaIdToDiscussionsTable extends Migration
{
    public function up()
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->unsignedInteger('fauna_id')->after('checklist_id');
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropForeign(['fauna_id']);
            $table->dropColumn('fauna_id');
        });
    }
}
