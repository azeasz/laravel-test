<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceChecklistIdToTaxaIdentifications extends Migration
{
    public function up()
    {
        Schema::table('taxa_identifications', function (Blueprint $table) {
            // Drop foreign key lama jika ada
            $table->dropForeign(['checklist_id']);

            // Tambah kolom baru
            $table->unsignedBigInteger('burnes_checklist_id')->nullable();
            $table->unsignedBigInteger('kupnes_checklist_id')->nullable();

            // Tambah foreign keys
            $table->foreign('burnes_checklist_id')
                  ->references('id')
                  ->on('fobi_checklists')
                  ->onDelete('cascade');

            $table->foreign('kupnes_checklist_id')
                  ->references('id')
                  ->on('fobi_checklists_kupnes')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('taxa_identifications', function (Blueprint $table) {
            $table->dropForeign(['burnes_checklist_id']);
            $table->dropForeign(['kupnes_checklist_id']);
            $table->dropColumn(['burnes_checklist_id', 'kupnes_checklist_id']);
        });
    }
}
