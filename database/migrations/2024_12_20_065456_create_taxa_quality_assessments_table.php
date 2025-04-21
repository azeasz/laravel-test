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
            $table->unsignedBigInteger('taxa_id');
            $table->enum('grade', ['research grade', 'needs ID', 'low quality ID', 'casual'])->default('needs ID');
            $table->boolean('has_date')->default(false);
            $table->boolean('has_location')->default(false);
            $table->boolean('has_media')->default(false);
            $table->boolean('is_wild')->default(true);
            $table->boolean('location_accurate')->default(true);
            $table->boolean('recent_evidence')->default(true);
            $table->boolean('related_evidence')->default(true);
            $table->boolean('needs_id')->default(true);
            $table->string('community_id_level')->nullable();
            $table->boolean('can_be_improved')->default(true);
            $table->integer('agreement_count')->default(0);
            $table->boolean('has_curator_decision')->default(false);
            $table->unsignedBigInteger('curator_id')->nullable();
            $table->text('curator_notes')->nullable();
            $table->timestamps();

            $table->foreign('taxa_id')->references('id')->on('fobi_checklist_taxas')->onDelete('cascade');
            $table->foreign('curator_id')->references('id')->on('fobi_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_quality_assessments');
    }
};
