<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('initiator_id');
            $table->unsignedBigInteger('disputed_identification_id');
            $table->text('reason');
            $table->enum('status', ['open', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('resolver_id')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('fobi_checklist_taxas');
            $table->foreign('initiator_id')->references('id')->on('fobi_users');
            $table->foreign('disputed_identification_id')->references('id')->on('taxa_identifications');
            $table->foreign('resolver_id')->references('id')->on('fobi_users');
        });

        Schema::create('taxa_dispute_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('agrees_with_dispute');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('dispute_id')->references('id')->on('taxa_disputes');
            $table->foreign('user_id')->references('id')->on('fobi_users');
            $table->unique(['dispute_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_dispute_votes');
        Schema::dropIfExists('taxa_disputes');
    }
};
