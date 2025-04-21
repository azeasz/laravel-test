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
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('image_id')->index('ratings_image_id_foreign');
            $table->unsignedInteger('user_id')->index('ratings_user_id_foreign');
            $table->integer('rating');
            $table->timestamps();
            $table->boolean('is_notified')->default(false);
            $table->boolean('is_read')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
