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
        Schema::create('fieldguide_flora', function (Blueprint $table) {
            $table->unsignedInteger('fieldguide_id')->index();
            $table->unsignedInteger('flora_id')->index();

            $table->primary(['fieldguide_id', 'flora_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fieldguide_flora');
    }
};
