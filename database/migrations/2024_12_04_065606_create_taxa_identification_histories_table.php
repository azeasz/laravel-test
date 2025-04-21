<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_identification_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('taxa_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action_type'); // 'initial', 'suggestion', 'change', 'withdraw'
            $table->string('scientific_name');
            $table->string('previous_name')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('checklist_id')
                  ->references('id')
                  ->on('fobi_checklist_taxas')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('taxa_identification_histories');
    }
};
