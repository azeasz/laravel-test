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
        Schema::table('checklist_fauna', function (Blueprint $table) {
            $table->foreign(['checklist_id'])->references(['id'])->on('checklists')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['fauna_id'])->references(['id'])->on('faunas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_fauna', function (Blueprint $table) {
            $table->dropForeign('checklist_fauna_checklist_id_foreign');
            $table->dropForeign('checklist_fauna_fauna_id_foreign');
        });
    }
};
