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
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign(['audio_id'])->references(['id'])->on('user_uploads')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['image_id'])->references(['id'])->on('user_uploads')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign('comments_audio_id_foreign');
            $table->dropForeign('comments_image_id_foreign');
            $table->dropForeign('comments_user_id_foreign');
        });
    }
};
