<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fauna_fieldguide', function (Blueprint $table) {
            $table->foreign(['fauna_id'])->references(['id'])->on('faunas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['fieldguide_id'])->references(['id'])->on('fieldguides')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fauna_fieldguide', function (Blueprint $table) {
            $table->dropForeign('fauna_fieldguide_fauna_id_foreign');
            $table->dropForeign('fauna_fieldguide_fieldguide_id_foreign');
        });
    }
};
