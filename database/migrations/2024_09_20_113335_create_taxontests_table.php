<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxontestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxontests', function (Blueprint $table) {
            $table->id();
            $table->string('kingdom');
            $table->integer('kingdomKey');
            $table->string('phylum');
            $table->string('cnamePhylum');
            $table->string('class');
            $table->string('cnameClass');
            $table->string('family');
            $table->string('cnameFamily');
            $table->string('genus');
            $table->string('cnameGenus');
            $table->string('species');
            $table->string('cnameSpecies');
            $table->string('taxonRank');
            $table->string('order');
            $table->integer('orderKey');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxontests');
    }
}
