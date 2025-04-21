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
        Schema::table('floraimg', function (Blueprint $table) {
            $table->foreign(['flora_id'])->references(['id'])->on('floras')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floraimg', function (Blueprint $table) {
            $table->dropForeign('floraimg_flora_id_foreign');
        });
    }
};
