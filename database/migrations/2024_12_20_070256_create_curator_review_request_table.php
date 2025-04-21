<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('curator_review_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('curator_id')->nullable();
            $table->text('reason');
            $table->text('curator_notes')->nullable();
            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('fobi_checklist_taxas');
            $table->foreign('requester_id')->references('id')->on('fobi_users');
            $table->foreign('curator_id')->references('id')->on('fobi_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('curator_review_requests');
    }
};
