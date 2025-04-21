<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateFeedbackTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('identifications');
        Schema::dropIfExists('suggestions');

        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('checklist_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('rating');
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });

        Schema::create('identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('checklist_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('identification');
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });

        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('checklist_id');
            $table->unsignedBigInteger('user_id');
            $table->string('suggested_name');
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('identifications');
        Schema::dropIfExists('suggestions');
    }
}
