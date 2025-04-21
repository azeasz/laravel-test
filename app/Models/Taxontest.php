<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxontest extends Model
{
    use HasFactory;

    protected $table = 'taxas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'taxon_key',
        'scientific_name',
        'accepted_taxon_key',
        'accepted_scientific_name',
        'taxon_rank',
        'taxonomic_status',
        'domain',
        'cname_domain',
        'superkingdom',
        'cname_superkingdom',
        'kingdom',
        'cname_kingdom',
        'kingdom_key',
        'subkingdom',
        'cname_subkingdom',
        'superphylum',
        'cname_superphylum',
        'phylum',
        'cname_phylum',
        'phylum_key',
        'subphylum',
        'cname_subphylum',
        'superclass',
        'cname_superclass',
        'class',
        'cname_class',
        'class_key',
        'subclass',
        'cname_subclass',
        'superorder',
        'cname_superorder',
        'order',
        'cname_order',
        'order_key',
        'suborder',
        'cname_suborder',
        'superfamily',
        'cname_superfamily',
        'family',
        'cname_family',
        'family_key',
        'subfamily',
        'cname_subfamily',
        'supertribe',
        'cname_supertribe',
        'tribe',
        'cname_tribe',
        'subtribe',
        'cname_subtribe',
        'genus',
        'cname_genus',
        'genus_key',
        'subgenus',
        'cname_subgenus',
        'species',
        'cname_species',
        'species_key',
        'subspecies',
        'cname_subspecies',
        'variety',
        'cname_variety',
        'iucn_red_list_category',
        'status_kepunahan',
        'status'
    ];

    public $timestamps = false;

    // Method untuk mendapatkan jumlah taxa aktif
    public static function getActiveTaxa()
    {
        return self::where('status', 'active')->count();
    }

    public static function getTotalTaxa()
    {
        return self::count();
    }
    public function updatedBy()
    {
        return $this->belongsTo(FobiUser::class, 'updated_by');
    }
}
