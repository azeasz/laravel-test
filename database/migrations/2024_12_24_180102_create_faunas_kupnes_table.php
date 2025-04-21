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
        Schema::create('faunas_kupnes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 10)->nullable();
            $table->string('nameId');
            $table->string('nameLat');
            $table->string('nameEn');
            $table->string('family');
            $table->string('keyword', 1000);
            $table->text('source');
            $table->boolean('is_protection')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faunas_kupnes');
    }
};
