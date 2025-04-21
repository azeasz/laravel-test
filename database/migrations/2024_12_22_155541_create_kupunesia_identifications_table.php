<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kupunesia_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('taxon_id');
            $table->string('identification_level');
            $table->text('notes')->nullable();
            $table->integer('agreement_count')->default(0);
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('observation_id')
                  ->references('id')
                  ->on('fobi_checklists_kupnes')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kupunesia_identifications');
    }
};
