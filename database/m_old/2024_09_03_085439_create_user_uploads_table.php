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
        Schema::create('user_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index('user_uploads_user_id_foreign');
            $table->string('uname')->nullable();
            $table->string('file_name');
            $table->string('photo_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('illustration_path')->nullable();
            $table->string('spectrogram_path')->nullable();
            $table->string('file_path')->nullable();
            $table->string('nameId');
            $table->string('species_name')->nullable();
            $table->string('location');
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->text('notes')->nullable();
            $table->string('audio_type')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();
            $table->unsignedInteger('fauna_id')->nullable()->index('user_uploads_fauna_id_foreign');
            $table->boolean('is_promoted')->default(false);
            $table->boolean('is_promoted_to_order')->default(false);
            $table->boolean('is_promoted_to_home')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_uploads');
    }
};
