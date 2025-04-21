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
        Schema::create('checklist_fauna', function (Blueprint $table) {
            $table->unsignedInteger('checklist_id')->index();
            $table->unsignedInteger('fauna_id')->index();
            $table->string('count', 10);
            $table->text('notes')->nullable();
            $table->integer('breeding')->default(0);
            $table->text('breeding_type_id')->nullable();
            $table->text('breeding_note')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->primary(['checklist_id', 'fauna_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_fauna');
    }
};
