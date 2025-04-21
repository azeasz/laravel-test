<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_similar_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->unsignedBigInteger('similar_taxa_id');
            $table->integer('confusion_count')->default(0);
            $table->string('similarity_type')->comment('genus, species, subspecies, etc');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('cascade');

            $table->foreign('similar_taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('cascade');

            // Mencegah duplikasi pasangan taxa
            $table->unique(['taxa_id', 'similar_taxa_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_similar_identifications');
    }
};
