<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('fauna_id');
            $table->unsignedInteger('checklist_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade'); // Tambahkan ini
        });
    }
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
