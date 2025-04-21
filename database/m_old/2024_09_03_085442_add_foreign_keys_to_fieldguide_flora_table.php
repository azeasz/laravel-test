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
        Schema::table('fieldguide_flora', function (Blueprint $table) {
            $table->foreign(['fieldguide_id'])->references(['id'])->on('fieldguides')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['flora_id'])->references(['id'])->on('floras')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fieldguide_flora', function (Blueprint $table) {
            $table->dropForeign('fieldguide_flora_fieldguide_id_foreign');
            $table->dropForeign('fieldguide_flora_flora_id_foreign');
        });
    }
};
