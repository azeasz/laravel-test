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
            $table->integer('taxonKey');
            $table->string('scientificName');
            $table->integer('acceptedTaxonKey')->nullable();
            $table->string('acceptedScientificName')->nullable();
            $table->string('taxonRank');
            $table->string('taxonomicStatus');
            $table->string('kingdom');
            $table->string('cnameKingdom');
            $table->integer('kingdomKey');
            $table->string('phylum');
            $table->string('cnamePhylum');
            $table->integer('phylumKey');
            $table->string('class');
            $table->string('cnameClass');
            $table->integer('classKey');
            $table->string('order');
            $table->string('cnameOrder');
            $table->integer('orderKey');
            $table->string('kategotiumum')->nullable();
            $table->string('family');
            $table->string('cnameFamily');
            $table->integer('familyKey');
            $table->string('genus');
            $table->string('cnameGenus');
            $table->integer('genusKey');
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
