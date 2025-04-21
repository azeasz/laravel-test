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
        Schema::create('token_management', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('user_id');
            $table->string('access_token')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->integer('is_active')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_management');
    }
};
