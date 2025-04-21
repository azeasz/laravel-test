<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_identification_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('taxa_id');
            $table->unsignedBigInteger('previous_taxa_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('action_type'); // 'initial', 'suggestion', 'change', 'withdraw'

            // Current Taxonomy - menggunakan TEXT untuk data yang panjang
            $table->text('scientific_name');
            $table->text('taxon_key')->nullable();
            $table->text('accepted_scientific_name')->nullable();
            $table->string('taxon_rank')->nullable();
            $table->string('taxonomic_status')->nullable();

            // Menyimpan data taksonomi current sebagai JSON
            $table->json('current_taxonomy')->nullable();

            // Menyimpan data taksonomi previous sebagai JSON
            $table->json('previous_taxonomy')->nullable();

            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('checklist_id')
                  ->references('id')
                  ->on('fobi_checklist_taxas')
                  ->onDelete('cascade');

            $table->foreign('taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('cascade');

            $table->foreign('previous_taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('set null');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_identification_histories');
    }
};
