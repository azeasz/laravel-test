<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taxas', function (Blueprint $table) {
            $table->unsignedInteger('burnes_fauna_id')->nullable()->after('id');
            $table->foreign('burnes_fauna_id')
                  ->references('id')
                  ->on('faunas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('taxas', function (Blueprint $table) {
            $table->dropForeign(['burnes_fauna_id']);
            $table->dropColumn('burnes_fauna_id');
        });
    }
};
