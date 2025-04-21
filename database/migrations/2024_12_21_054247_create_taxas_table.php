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
        Schema::create('taxas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('taxon_key')->nullable();
            $table->string('scientific_name');
            $table->unsignedBigInteger('accepted_taxon_key')->nullable();
            $table->string('accepted_scientific_name')->nullable();
            $table->string('taxon_rank')->index('idx_tax_rank');
            $table->string('taxonomic_status')->nullable()->index('idx_tax_status');
            $table->string('domain')->nullable();
            $table->string('cname_domain')->nullable();
            $table->string('superkingdom')->nullable();
            $table->string('cname_superkingdom')->nullable();
            $table->string('kingdom')->nullable()->index('idx_kingdom');
            $table->string('cname_kingdom')->nullable();
            $table->unsignedBigInteger('kingdom_key')->nullable();
            $table->string('subkingdom')->nullable();
            $table->string('cname_subkingdom')->nullable();
            $table->string('superphylum')->nullable();
            $table->string('cname_superphylum')->nullable();
            $table->string('phylum')->nullable()->index('idx_phylum');
            $table->string('cname_phylum')->nullable();
            $table->unsignedBigInteger('phylum_key')->nullable();
            $table->string('subphylum')->nullable();
            $table->string('cname_subphylum')->nullable();
            $table->string('superclass')->nullable();
            $table->string('cname_superclass')->nullable();
            $table->string('class')->nullable()->index('idx_class');
            $table->string('cname_class')->nullable();
            $table->decimal('class_key', 10, 1)->nullable();
            $table->string('subclass')->nullable();
            $table->string('cname_subclass')->nullable();
            $table->string('infraclass')->nullable();
            $table->string('cname_infraclass')->nullable();
            $table->string('subterclass')->nullable();
            $table->string('superorder')->nullable();
            $table->string('cname_superorder')->nullable();
            $table->string('order')->nullable()->index('idx_order');
            $table->string('cname_order')->nullable();
            $table->decimal('order_key', 10, 1)->nullable();
            $table->string('suborder')->nullable();
            $table->string('cname_suborder')->nullable();
            $table->string('infraorder')->nullable();
            $table->string('superfamily')->nullable();
            $table->string('cname_superfamily')->nullable();
            $table->string('family')->nullable()->index('idx_family');
            $table->string('cname_family')->nullable();
            $table->decimal('family_key', 10, 1)->nullable();
            $table->string('subfamily')->nullable();
            $table->string('cname_subfamily')->nullable();
            $table->string('supertribe')->nullable();
            $table->string('cname_supertribe')->nullable();
            $table->string('tribe')->nullable();
            $table->string('cname_tribe')->nullable();
            $table->string('subtribe')->nullable();
            $table->string('cname_subtribe')->nullable();
            $table->string('genus')->nullable()->index('idx_genus');
            $table->string('cname_genus')->nullable();
            $table->decimal('genus_key', 10, 1)->nullable();
            $table->string('subgenus')->nullable();
            $table->string('cname_subgenus')->nullable();
            $table->string('species')->nullable()->index('idx_species');
            $table->string('cname_species')->nullable();
            $table->decimal('species_key', 10, 1)->nullable();
            $table->string('subspecies')->nullable();
            $table->string('cname_subspecies')->nullable();
            $table->string('variety')->nullable();
            $table->string('cname_variety')->nullable();
            $table->string('iucn_red_list_category')->nullable();
            $table->string('status_kepunahan')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable()->index('taxa_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('taxa_updated_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxas');
    }
};
