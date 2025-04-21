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
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->foreign(['fauna_id'])->references(['id'])->on('faunas')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_uploads', function (Blueprint $table) {
            $table->dropForeign('user_uploads_fauna_id_foreign');
            $table->dropForeign('user_uploads_user_id_foreign');
        });
    }
};
