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
        Schema::create('fobi_checklist_fauna_imgs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedInteger('fauna_id');
            $table->string('images', 255)->nullable();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('checklist_id')->references('id')->on('fobi_checklists')->onDelete('cascade');
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fobi_checklist_fauna_imgs');
    }
};
