<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fobi_checklist_taxas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->unsignedBigInteger('user_id');
            $table->string('observation_details')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('taxa_id')
                  ->references('id')
                  ->on('taxas')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('fobi_users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_checklist_taxas');
    }
};
