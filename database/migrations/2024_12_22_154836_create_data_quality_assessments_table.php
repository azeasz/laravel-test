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
        Schema::create('data_quality_assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('observation_id')->index('data_quality_assessments_observation_id_foreign');
            $table->unsignedInteger('fauna_id')->index('data_quality_assessments_fauna_id_foreign');
            $table->enum('grade', ['casual', 'needs ID', 'low quality ID', 'research grade'])->default('casual');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_quality_assessments');
    }
};
