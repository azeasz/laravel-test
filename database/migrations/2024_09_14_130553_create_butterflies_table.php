<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateButterfliesTable extends Migration
{
    public function up()
    {
        Schema::create('butterflies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('observation_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('count');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('butterflies');
    }
}
