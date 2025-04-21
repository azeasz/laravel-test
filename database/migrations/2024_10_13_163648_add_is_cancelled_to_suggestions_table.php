<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCancelledToSuggestionsTable extends Migration
{
    public function up()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false)->after('suggested_name');
        });
    }

    public function down()
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropColumn('is_cancelled');
        });
    }
}
