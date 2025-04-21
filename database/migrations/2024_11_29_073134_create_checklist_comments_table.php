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
// Migration for comments and discussions
Schema::create('checklist_comments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('checklist_id');
    $table->unsignedBigInteger('user_id');
    $table->text('comment');
    $table->timestamps();

    $table->foreign('checklist_id')->references('id')->on('fobi_checklist_taxas')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
});    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_comments');
    }
};
