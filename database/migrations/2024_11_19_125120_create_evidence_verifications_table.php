<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evidence_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id');
            $table->string('observation_type'); // 'burungnesia', 'kupunesia', atau 'general'
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_valid_evidence');
            $table->boolean('is_recent');
            $table->boolean('is_related');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');

            // Menggunakan nama yang lebih pendek untuk unique constraint
            $table->unique(
                ['observation_id', 'observation_type', 'user_id'],
                'evidence_verif_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('evidence_verifications');
    }
};
