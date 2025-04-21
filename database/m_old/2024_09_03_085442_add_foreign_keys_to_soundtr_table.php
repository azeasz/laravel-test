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
        Schema::table('soundtr', function (Blueprint $table) {
            $table->foreign(['lang_code'])->references(['lang_code'])->on('languages')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['sound_id'])->references(['id'])->on('sounds')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soundtr', function (Blueprint $table) {
            $table->dropForeign('soundtr_lang_code_foreign');
            $table->dropForeign('soundtr_sound_id_foreign');
        });
    }
};
