<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wild_status_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id');
            $table->string('observation_type'); // 'burungnesia', 'kupunesia', atau 'general'
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_wild');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');

            // Prevent duplicate votes
            $table->unique(['observation_id', 'observation_type', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wild_status_votes');
    }
};
