<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fobi_taxa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('scientificName');
            $table->string('taxonRank');
            $table->string('kingdom')->nullable();
            $table->string('phylum')->nullable();
            $table->string('class')->nullable();
            $table->string('order')->nullable();
            $table->string('family')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();
            $table->string('taxonomicStatus')->nullable();
            $table->string('taxa_type')->comment('animalia, plantae, fungi, etc');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_taxa');
    }
};
