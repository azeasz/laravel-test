<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel utama taxa
        Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxon_key')->nullable();
            $table->string('scientific_name');
            $table->unsignedBigInteger('accepted_taxon_key')->nullable();
            $table->string('accepted_scientific_name')->nullable();
            $table->string('taxon_rank');
            $table->string('taxonomic_status');

            // Hierarki taksonomi lengkap dengan index terpisah
            $table->string('domain')->nullable();
            $table->string('cname_domain')->nullable();
            $table->string('superkingdom')->nullable();
            $table->string('cname_superkingdom')->nullable();
            $table->string('kingdom')->nullable();
            $table->string('cname_kingdom')->nullable();
            $table->unsignedBigInteger('kingdom_key')->nullable();
            $table->string('subkingdom')->nullable();
            $table->string('cname_subkingdom')->nullable();
            $table->string('superphylum')->nullable();
            $table->string('cname_superphylum')->nullable();
            $table->string('phylum')->nullable();
            $table->string('cname_phylum')->nullable();
            $table->unsignedBigInteger('phylum_key')->nullable();
            $table->string('subphylum')->nullable();
            $table->string('cname_subphylum')->nullable();
            $table->string('superclass')->nullable();
            $table->string('cname_superclass')->nullable();
            $table->string('class')->nullable();
            $table->string('cname_class')->nullable();
            $table->decimal('class_key', 10, 1)->nullable();
            $table->string('subclass')->nullable();
            $table->string('cname_subclass')->nullable();
            $table->string('infraclass')->nullable();
            $table->string('cname_infraclass')->nullable();
            $table->string('subterclass')->nullable();
            $table->string('superorder')->nullable();
            $table->string('cname_superorder')->nullable();
            $table->string('order')->nullable();
            $table->string('cname_order')->nullable();
            $table->decimal('order_key', 10, 1)->nullable();
            $table->string('suborder')->nullable();
            $table->string('cname_suborder')->nullable();
            $table->string('infraorder')->nullable();
            $table->string('superfamily')->nullable();
            $table->string('cname_superfamily')->nullable();
            $table->string('family')->nullable();
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
            $table->string('genus')->nullable();
            $table->string('cname_genus')->nullable();
            $table->decimal('genus_key', 10, 1)->nullable();
            $table->string('subgenus')->nullable();
            $table->string('cname_subgenus')->nullable();
            $table->string('species')->nullable();
            $table->string('cname_species')->nullable();
            $table->decimal('species_key', 10, 1)->nullable();
            $table->string('subspecies')->nullable();
            $table->string('cname_subspecies')->nullable();
            $table->string('variety')->nullable();
            $table->string('cname_variety')->nullable();
            $table->string('iucn_red_list_category')->nullable();
            $table->string('status_kepunahan')->nullable();

            // Metadata dan tracking
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('fobi_users');
            $table->foreign('updated_by')->references('id')->on('fobi_users');

            // Indexes terpisah untuk menghindari nama yang terlalu panjang
            $table->index('kingdom', 'idx_kingdom');
            $table->index('phylum', 'idx_phylum');
            $table->index('class', 'idx_class');
            $table->index('order', 'idx_order');
            $table->index('family', 'idx_family');
            $table->index('genus', 'idx_genus');
            $table->index('species', 'idx_species');
            $table->index('taxonomic_status', 'idx_tax_status');
            $table->index('taxon_rank', 'idx_tax_rank');
        });
        // Tabel untuk media
        Schema::create('taxa_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->string('media_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('uploaded_by');

            $table->foreign('taxa_id')->references('id')->on('taxa')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('fobi_users');
        });

        // Tabel untuk riwayat perubahan
        Schema::create('taxa_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxa_id');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('changed_at');
            $table->unsignedBigInteger('changed_by')->nullable();

            $table->foreign('taxa_id')->references('id')->on('taxa')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('fobi_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxa_media');
        Schema::dropIfExists('taxa_history');
        Schema::dropIfExists('taxa');
    }
};
