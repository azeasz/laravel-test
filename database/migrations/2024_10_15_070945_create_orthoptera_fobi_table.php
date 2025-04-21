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
        Schema::create('orthoptera_fobi', function (Blueprint $table) {
            $table->integer('taxonKey')->nullable();
            $table->text('scientificName')->nullable();
            $table->integer('acceptedTaxonKey')->nullable();
            $table->text('acceptedScientificName')->nullable();
            $table->text('taxonRank')->nullable();
            $table->text('taxonomicStatus')->nullable();
            $table->text('domain')->nullable();
            $table->text('cnamedomain')->nullable();
            $table->text('superkingdom')->nullable();
            $table->text('cnamesuperkingdom')->nullable();
            $table->text('kingdom')->nullable();
            $table->text('cnameKingdom')->nullable();
            $table->integer('kingdomKey')->nullable();
            $table->text('subkingdom')->nullable();
            $table->text('cnamesubkingdom')->nullable();
            $table->text('superphylum')->nullable();
            $table->text('cnamesuperphylum')->nullable();
            $table->text('phylum')->nullable();
            $table->text('cnamePhylum')->nullable();
            $table->integer('phylumKey')->nullable();
            $table->text('subphylum')->nullable();
            $table->text('cnmaesubphylum')->nullable();
            $table->text('superclass')->nullable();
            $table->text('cnamesuperclass')->nullable();
            $table->text('class')->nullable();
            $table->text('cnameClass')->nullable();
            $table->integer('classKey')->nullable();
            $table->text('subclass')->nullable();
            $table->text('cnamesubclass')->nullable();
            $table->text('superorder')->nullable();
            $table->text('cnamesuperorder')->nullable();
            $table->text('order')->nullable();
            $table->text('cnameOrder')->nullable();
            $table->integer('orderKey')->nullable();
            $table->text('suborder')->nullable();
            $table->text('cnamesuborder')->nullable();
            $table->text('superfamily')->nullable();
            $table->text('cnamesuperfamily')->nullable();
            $table->text('family')->nullable();
            $table->text('cnamefamily')->nullable();
            $table->integer('familyKey')->nullable();
            $table->text('subfamily')->nullable();
            $table->text('cnamesubfamily')->nullable();
            $table->text('supertribe')->nullable();
            $table->text('cnamesupertribe')->nullable();
            $table->text('tribe')->nullable();
            $table->text('cnametribe')->nullable();
            $table->text('subtribe')->nullable();
            $table->text('cnamesubtribe')->nullable();
            $table->text('genus')->nullable();
            $table->text('cnameGenus')->nullable();
            $table->integer('genusKey')->nullable();
            $table->text('subgenus')->nullable();
            $table->text('cnamesubgenus')->nullable();
            $table->text('species')->nullable();
            $table->text('cnameSpecies')->nullable();
            $table->integer('speciesKey')->nullable();
            $table->text('subspecies')->nullable();
            $table->text('cnmaesubspecies')->nullable();
            $table->text('variety')->nullable();
            $table->text('cnamevariety')->nullable();
            $table->text('iucnRedListCategory')->nullable();
            $table->text('statuskepunahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orthoptera_fobi');
    }
};
