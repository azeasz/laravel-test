<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentifikasiObservasisTable extends Migration
{
    public function up()
    {
        Schema::create('identifikasi_observasis', function (Blueprint $table) {
            $table->id();
            $table->string('species_name');
            $table->string('common_name')->nullable();
            $table->text('description')->nullable();
            $table->date('observed_at');
            $table->date('uploaded_at');
            $table->integer('rating')->default(0);
            $table->integer('ratings_count')->default(0);
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('identifikasi_observasis');
    }
}
