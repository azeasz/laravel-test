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
        Schema::create('admin_taxa_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->string('action');
            $table->json('changes');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->foreign('taxa_id')->references('id')->on('taxas');
            $table->foreign('user_id')->references('id')->on('fobi_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_taxa_histories');
    }
};
