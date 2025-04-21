<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_quality_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_taxa_id');
            $table->enum('grade', ['casual', 'needs ID', 'research grade'])->default('casual');
            $table->boolean('has_date')->default(false);
            $table->boolean('has_location')->default(false);
            $table->boolean('has_media')->default(false);
            $table->boolean('is_wild')->default(true);
            $table->boolean('location_accurate')->default(true);
            $table->boolean('recent_evidence')->default(true);
            $table->boolean('related_evidence')->default(true);
            $table->boolean('needs_id')->default(false);
            $table->string('community_id_level')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('checklist_taxa_id')
                  ->references('id')
                  ->on('fobi_checklist_taxas')
                  ->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('taxa_quality_assessments');
    }
};
