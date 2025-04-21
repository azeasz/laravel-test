<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('observation_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            // ID untuk masing-masing sumber (hanya satu yang akan diisi)
            $table->unsignedBigInteger('observation_id')->nullable();
            $table->unsignedBigInteger('burnes_checklist_id')->nullable();
            $table->unsignedBigInteger('kupnes_checklist_id')->nullable();
            // Kolom source untuk menandai asal data
            $table->enum('source', ['fobi', 'burungnesia', 'kupunesia']);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key ke user
            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('observation_comments');
    }
};
