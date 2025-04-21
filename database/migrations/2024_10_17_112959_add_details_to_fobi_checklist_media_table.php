<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToFobiChecklistMediaTable extends Migration
{
    public function up()
    {
        Schema::table('fobi_checklist_media', function (Blueprint $table) {
            $table->date('date')->after('scientific_name');
            $table->string('location', 255)->after('date');
            $table->string('habitat', 255)->after('location');
            $table->text('description')->after('habitat');
        });
    }

    public function down()
    {
        Schema::table('fobi_checklist_media', function (Blueprint $table) {
            $table->dropColumn(['date', 'location', 'habitat', 'description']);
        });
    }
}
