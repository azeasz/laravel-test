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
        Schema::create('fauna_fieldguide', function (Blueprint $table) {
            $table->unsignedInteger('fauna_id')->index();
            $table->unsignedInteger('fieldguide_id')->index();

            $table->primary(['fauna_id', 'fieldguide_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fauna_fieldguide');
    }
};
