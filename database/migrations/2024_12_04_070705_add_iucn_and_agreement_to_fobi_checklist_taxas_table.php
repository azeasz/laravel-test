<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fobi_checklist_taxas', function (Blueprint $table) {
            $table->string('iucn_status')->nullable()->after('observation_details');
            $table->integer('agreement_count')->default(0)->after('iucn_status');
            $table->string('original_scientific_name')->nullable()->after('scientific_name');
            $table->unsignedBigInteger('original_taxa_id')->nullable()->after('taxa_id');

            $table->foreign('original_taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('fobi_checklist_taxas', function (Blueprint $table) {
            $table->dropForeign(['original_taxa_id']);
            $table->dropColumn([
                'iucn_status',
                'agreement_count',
                'original_scientific_name',
                'original_taxa_id'
            ]);
        });
    }
};
