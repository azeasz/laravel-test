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
        Schema::create('faunacons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fauna_id')->index();
            $table->string('unique_id', 20);
            $table->string('lang_code', 5)->index('lang_code');
            $table->tinyInteger('conserv');
            $table->string('author');
            $table->string('status');
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
        Schema::dropIfExists('faunacons');
    }
};
