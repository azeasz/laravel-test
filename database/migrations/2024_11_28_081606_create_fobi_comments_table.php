<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fobi_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('checklist_taxa_id');
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('fobi_users');
            $table->foreign('checklist_taxa_id')->references('id')->on('fobi_checklist_taxas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_comments');
    }
};
