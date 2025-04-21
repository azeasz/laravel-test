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
        Schema::create('fobi_checklist_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_id')->nullable()->index('fobi_checklist_media_checklist_id_foreign');
            $table->string('media_type', 50);
            $table->string('file_path');
            $table->string('scientific_name');
            $table->date('date');
            $table->string('location');
            $table->string('habitat')->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('status')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('spectrogram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fobi_checklist_media');
    }
};
