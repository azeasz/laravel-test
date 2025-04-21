<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kupunesia_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('checklist_id')
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
        Schema::dropIfExists('kupunesia_comments');
    }
};
