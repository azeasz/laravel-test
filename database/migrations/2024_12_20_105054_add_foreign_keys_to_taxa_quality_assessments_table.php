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
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->foreign(['taxa_id'], 'taxa_quality_assessments_checklist_taxa_id_foreign')->references(['id'])->on('fobi_checklist_taxas')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['taxon_id'])->references(['id'])->on('taxas')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->dropForeign('taxa_quality_assessments_checklist_taxa_id_foreign');
            $table->dropForeign('taxa_quality_assessments_taxon_id_foreign');
        });
    }
};
