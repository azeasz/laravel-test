<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fobi_checklist_taxas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('media_id')->nullable();
            $table->string('scientific_name');
            $table->string('class')->nullable();
            $table->string('order')->nullable();
            $table->string('family')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('observation_details')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');

            $table->foreign('media_id')
                  ->references('id')
                  ->on('fobi_checklist_media')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_checklist_taxas');
    }
};
