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
        Schema::create('contact_us', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('user_id')->default(0);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('question')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('admin_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};
