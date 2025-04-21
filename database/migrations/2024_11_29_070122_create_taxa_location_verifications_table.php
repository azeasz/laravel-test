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
        Schema::create('taxa_location_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('fobi_checklist_taxas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('fobi_users')->onDelete('cascade');
            $table->boolean('is_accurate');
            $table->string('comment', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxa_location_verifications');
    }
};
