<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('taxontests', function (Blueprint $table) {
            $table->id();
            $table->string('kingdom');
            $table->integer('kingdomKey');
            $table->string('phylum');
            $table->string('cnamePhylum');
            $table->string('cnameClass');
            $table->string('family');
            $table->string('cnameFamily');
            $table->string('genus');
            $table->string('species');
            $table->string('cnameSpecies');
            $table->string('taxonRank');
            $table->string('order');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxontests');
    }
};
