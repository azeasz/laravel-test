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
        Schema::create('floratr', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('flora_id')->index();
            $table->string('lang_code', 5)->index('lang_code');
            $table->text('description');
            $table->text('distribution');
            $table->string('flowering');
            $table->string('group');
            $table->string('shape');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floratr');
    }
};
