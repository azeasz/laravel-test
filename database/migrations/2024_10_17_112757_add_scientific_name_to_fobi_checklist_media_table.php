<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScientificNameToFobiChecklistMediaTable extends Migration
{
    public function up()
    {
        Schema::table('fobi_checklist_media', function (Blueprint $table) {
            $table->string('scientific_name', 255)->after('file_path');
        });
    }

    public function down()
    {
        Schema::table('fobi_checklist_media', function (Blueprint $table) {
            $table->dropColumn('scientific_name');
        });
    }
}
