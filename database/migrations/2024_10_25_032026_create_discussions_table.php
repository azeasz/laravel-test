<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscussionsTable extends Migration
{
    public function up()
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('checklist_id');
            $table->unsignedBigInteger('fobi_checklist_id')->nullable(); // Tambahkan ini
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->unsignedBigInteger('suggestion_id')->nullable();
            $table->unsignedBigInteger('identification_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('fobi_checklist_id')->references('id')->on('fobi_checklists')->onDelete('cascade'); // Tambahkan ini
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('suggestion_id')->references('id')->on('suggestions')->onDelete('cascade');
            $table->foreign('identification_id')->references('id')->on('identifications')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discussions');
    }
}
