<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop tabel lama jika ada
        Schema::dropIfExists('fobi_checklist_media');

        // Buat tabel baru
        Schema::create('fobi_checklist_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->string('media_type');
            $table->string('file_path');
            $table->string('spectrogram')->nullable();
            $table->string('scientific_name');
            $table->string('location');
            $table->string('habitat')->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            // Tambahkan foreign key
            $table->foreign('checklist_id')
                  ->references('id')
                  ->on('fobi_checklist_taxas')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_checklist_media');
    }
};
