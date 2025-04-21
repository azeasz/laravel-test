<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFobiChecklistFaunasV1Table extends Migration
{
    public function up()
    {
        Schema::create('fobi_checklist_faunasV1', function (Blueprint $table) {
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedInteger('fauna_id');
            $table->string('count', 10);
            $table->text('notes')->nullable();
            $table->integer('breeding')->default(0);
            $table->text('breeding_type_id')->nullable();
            $table->text('breeding_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Primary key
            $table->primary(['checklist_id', 'fauna_id']);

            // Foreign key constraints
            $table->foreign('checklist_id')->references('id')->on('fobi_checklists')->onDelete('cascade');
            $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_checklist_faunasV1');
    }
}
