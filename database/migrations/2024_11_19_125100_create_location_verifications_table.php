<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('location_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id');
            $table->string('observation_type'); // 'burungnesia', 'kupunesia', atau 'general'
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_accurate');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_verifications');
    }
};
