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
        Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->integer('taxonKey')->nullable(false);
            $table->string('scientificName')->nullable();
            $table->integer('acceptedTaxonKey')->nullable();
            $table->string('acceptedScientificName')->nullable();
            $table->string('taxonRank')->nullable();
            $table->string('taxonomicStatus')->nullable();
            $table->string('kingdom')->nullable();
            $table->string('cnameKingdom')->nullable();
            $table->integer('kingdomKey')->nullable();
            $table->string('phylum')->nullable();
            $table->string('cnamePhylum')->nullable();
            $table->integer('phylumKey')->nullable();
            $table->string('class')->nullable();
            $table->string('cnameClass')->nullable();
            $table->integer('classKey')->nullable();
            $table->string('order')->nullable();
            $table->string('cnameOrder')->nullable();
            $table->integer('orderKey')->nullable();
            $table->string('kategotiumum')->nullable();
            $table->string('family')->nullable();
            $table->string('cnameFamily')->nullable();
            $table->integer('familyKey')->nullable();
            $table->string('genus')->nullable();
            $table->string('cnameGenus')->nullable();
            $table->integer('genusKey')->nullable();
            $table->string('species')->nullable();
            $table->string('cnameSpecies')->nullable();
            $table->integer('speciesKey')->nullable();
            $table->string('iucnRedListCategory')->nullable();
            $table->string('subspecies')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa');
    }
};
