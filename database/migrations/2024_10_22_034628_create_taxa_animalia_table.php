<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taxa_animalia', function (Blueprint $table) {
            $table->integer('taxonKey')->nullable();
            $table->string('scientificName')->nullable();
            $table->integer('acceptedTaxonKey')->nullable();
            $table->string('acceptedScientificName')->nullable();
            $table->string('taxonRank')->nullable();
            $table->string('taxonomicStatus')->nullable();
            $table->string('domain')->nullable();
            $table->string('cnamedomain')->nullable();
            $table->string('superkingdom')->nullable();
            $table->string('cnamesuperkingdom')->nullable();
            $table->string('kingdom')->nullable();
            $table->string('cnameKingdom')->nullable();
            $table->integer('kingdomKey')->nullable();
            $table->string('subkingdom')->nullable();
            $table->string('cnamesubkingdom')->nullable();
            $table->string('superphylum')->nullable();
            $table->string('cnamesuperphylum')->nullable();
            $table->string('phylum')->nullable();
            $table->string('cnamePhylum')->nullable();
            $table->integer('phylumKey')->nullable();
            $table->string('subphylum')->nullable();
            $table->string('cnamesubphylum')->nullable();
            $table->string('superclass')->nullable();
            $table->string('cnamesuperclass')->nullable();
            $table->string('class')->nullable();
            $table->text('cnameClass')->nullable();
            $table->integer('classKey')->nullable();
            $table->string('subclass')->nullable();
            $table->string('cnamesubclass')->nullable();
            $table->string('superorder')->nullable();
            $table->string('cnamesuperorder')->nullable();
            $table->string('order')->nullable();
            $table->text('cnameOrder')->nullable();
            $table->integer('orderKey')->nullable();
            $table->string('suborder')->nullable();
            $table->string('cnamesuborder')->nullable();
            $table->string('superfamily')->nullable();
            $table->string('cnamesuperfamily')->nullable();
            $table->string('family')->nullable();
            $table->text('cnameFamily')->nullable();
            $table->integer('familyKey')->nullable();
            $table->string('subfamily')->nullable();
            $table->string('cnamesubfamily')->nullable();
            $table->string('supertribe')->nullable();
            $table->string('cnamesupertribe')->nullable();
            $table->string('tribe')->nullable();
            $table->string('cnametribe')->nullable();
            $table->string('subtribe')->nullable();
            $table->string('cnamesubtribe')->nullable();
            $table->string('genus')->nullable();
            $table->text('cnameGenus')->nullable();
            $table->string('genusKey')->nullable();
            $table->string('subgenus')->nullable();
            $table->string('cnamesubgenus')->nullable();
            $table->string('species')->nullable();
            $table->text('cnameSpecies')->nullable();
            $table->string('speciesKey')->nullable();
            $table->string('subspecies')->nullable();
            $table->string('cnamesubspecies')->nullable();
            $table->string('variety')->nullable();
            $table->string('cnamevariety')->nullable();
            $table->string('iucnRedListCategory')->nullable();
            $table->string('statuskepunahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxa_animalia');
    }
};
