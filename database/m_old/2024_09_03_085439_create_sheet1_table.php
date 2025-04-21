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
        Schema::create('sheet1', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('fieldguide_id')->nullable();
            $table->string('fname', 12)->nullable();
            $table->string('lname', 14)->nullable();
            $table->string('email', 28)->nullable();
            $table->string('username', 18)->nullable();
            $table->string('password', 60)->nullable();
            $table->integer('level')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('organization', 61)->nullable();
            $table->string('ipp_addr', 10)->nullable();
            $table->string('created_at', 10)->nullable();
            $table->string('updated_at', 10)->nullable();
            $table->string('remember_token', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet1');
    }
};
