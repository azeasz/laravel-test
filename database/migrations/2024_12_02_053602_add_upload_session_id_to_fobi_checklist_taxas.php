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
        Schema::table('fobi_checklist_taxas', function (Blueprint $table) {
            $table->string('upload_session_id')->nullable()->after('observation_details');
            $table->index(['taxa_id', 'user_id', 'upload_session_id', 'created_at'], 'fobi_checklist_taxa_upload_session_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fobi_checklist_taxas', function (Blueprint $table) {
            $table->dropIndex('fobi_checklist_taxa_upload_session_index');
            $table->dropColumn('upload_session_id');
        });
    }
};
