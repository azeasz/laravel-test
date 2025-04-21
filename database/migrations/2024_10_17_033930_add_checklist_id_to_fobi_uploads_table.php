<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('fobi_uploads', function (Blueprint $table) {
            $table->unsignedInteger('checklist_id')->nullable()->after('is_identified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('fobi_uploads', function (Blueprint $table) {
            $table->dropColumn('checklist_id');
        });
    }
};
