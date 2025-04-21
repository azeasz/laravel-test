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
        Schema::create('taxa_identifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checklist_id')->index('taxa_identifications_checklist_id_foreign');
            $table->unsignedBigInteger('user_id')->index('taxa_identifications_user_id_foreign');
            $table->unsignedBigInteger('taxon_id')->index('taxa_identifications_taxon_id_foreign');
            $table->string('identification_level');
            $table->string('comment', 500)->nullable();
            $table->timestamps();
            $table->integer('agrees_with_id')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_agreed')->nullable()->default(false);
            $table->boolean('is_first')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxa_identifications');
    }
};
