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
        Schema::create('order_faunas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ordo_order')->nullable();
            $table->string('ordo')->nullable();
            $table->unsignedInteger('famili_order')->nullable();
            $table->string('famili')->nullable();
            $table->string('iucn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_faunas');
    }
};
