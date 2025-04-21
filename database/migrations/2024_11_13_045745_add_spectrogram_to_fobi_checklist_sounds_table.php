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
        Schema::table('fobi_checklist_sounds', function (Blueprint $table) {
            $table->string('spectrogram', 255)->nullable()->after('sound_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fobi_checklist_sounds', function (Blueprint $table) {
            $table->dropColumn('spectrogram');
        });
    }
};
