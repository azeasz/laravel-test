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
        Schema::table('birds', function (Blueprint $table) {
            $table->foreign('observation_id')->references('id')->on('observations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birds', function (Blueprint $table) {
            $table->dropForeign(['observation_id']);
        });
    }
};
