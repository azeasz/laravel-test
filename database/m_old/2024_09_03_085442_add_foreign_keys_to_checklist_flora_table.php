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
        Schema::table('checklist_flora', function (Blueprint $table) {
            $table->foreign(['checklist_id'])->references(['id'])->on('checklists')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['flora_id'])->references(['id'])->on('floras')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_flora', function (Blueprint $table) {
            $table->dropForeign('checklist_flora_checklist_id_foreign');
            $table->dropForeign('checklist_flora_flora_id_foreign');
        });
    }
};
