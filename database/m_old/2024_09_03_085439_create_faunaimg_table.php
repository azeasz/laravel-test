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
        Schema::create('faunaimg', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fauna_id')->index();
            $table->string('path');
            $table->string('images')->nullable();
            $table->text('filename');
            $table->string('credit');
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
        Schema::dropIfExists('faunaimg');
    }
};
