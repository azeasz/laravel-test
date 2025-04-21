<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('community_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('observation_id');
            $table->string('observation_type'); // 'burungnesia', 'kupunesia', atau 'general'
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('taxon_id');
            $table->string('identification_level'); // species, genus, family, etc.
            $table->text('notes')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');

            $table->foreign('taxon_id')
                  ->references('id')
                  ->on('fobi_taxa')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_identifications');
    }
};
