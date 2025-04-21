<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('flag_type', [
                'identification', // Masalah dengan identifikasi
                'location',      // Masalah dengan lokasi
                'media',         // Masalah dengan foto/audio
                'date',         // Masalah dengan tanggal
                'other'         // Masalah lainnya
            ]);
            $table->text('reason');
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('checklist_id')->references('id')->on('fobi_checklist_taxas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('fobi_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_flags');
    }
};
