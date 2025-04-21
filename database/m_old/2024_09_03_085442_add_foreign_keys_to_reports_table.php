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
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign(['audio_id'])->references(['id'])->on('user_uploads')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['image_id'])->references(['id'])->on('user_uploads')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_audio_id_foreign');
            $table->dropForeign('reports_image_id_foreign');
        });
    }
};
