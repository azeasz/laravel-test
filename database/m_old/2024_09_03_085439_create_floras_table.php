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
        Schema::create('floras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 10)->nullable();
            $table->string('nameId');
            $table->string('nameLat');
            $table->string('nameEn');
            $table->string('family');
            $table->string('height');
            $table->string('leafSize');
            $table->string('flowerDiameter');
            $table->string('keyword');
            $table->text('source');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floras');
    }
};
