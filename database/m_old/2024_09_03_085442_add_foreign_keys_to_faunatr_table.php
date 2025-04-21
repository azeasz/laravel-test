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
        Schema::table('faunatr', function (Blueprint $table) {
            $table->foreign(['fauna_id'])->references(['id'])->on('faunas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['lang_code'])->references(['lang_code'])->on('languages')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faunatr', function (Blueprint $table) {
            $table->dropForeign('faunatr_fauna_id_foreign');
            $table->dropForeign('faunatr_lang_code_foreign');
        });
    }
};
