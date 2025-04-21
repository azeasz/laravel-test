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
            $table->unsignedBigInteger('taxon_id')->after('taxa_id')->nullable();

            // Foreign key ke tabel taxa
            $table->foreign('taxon_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->dropForeign(['taxon_id']);
            $table->dropColumn('taxon_id');
        });
    }
};
