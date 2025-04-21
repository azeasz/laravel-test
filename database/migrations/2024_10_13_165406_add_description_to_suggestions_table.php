<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToSuggestionsTable extends Migration
{
    public function up()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('suggested_name');
        });
    }

    public function down()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
