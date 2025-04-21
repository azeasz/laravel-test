<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxaAnimalia extends Model
{
    use HasFactory;

    protected $table = 'taxa_animalias';

    protected $fillable = [
        'taxonKey',
        'scientificName',
        'acceptedTaxonKey',
        'acceptedScientificName',
        'taxonRank',
        'taxonomicStatus',
        'domain',
        'cnamedomain',
        'superkingdom',
        'cnamesuperkingdom',
        'kingdom',
        'cnameKingdom',
        'kingdomKey',
        'subkingdom',
        'cnamesubkingdom',
        'superphylum',
        'cnamesuperphylum',
        'phylum',
        'cnamePhylum',
        'phylumKey',
        'subphylum',
        'cnamesubphylum',
        'superclass',
        'cnamesuperclass',
        'class',
        'cnameClass',
        'classKey',
        'subclass',
        'cnamesubclass',
        'superorder',
        'cnamesuperorder',
        'order',
        'cnameOrder',
        'orderKey',
        'suborder',
        'cnamesuborder',
        'superfamily',
        'cnamesuperfamily',
        'family',
        'cnameFamily',
        'familyKey',
        'subfamily',
        'cnamesubfamily',
        'supertribe',
        'cnamesupertribe',
        'tribe',
        'cnametribe',
        'subtribe',
        'cnamesubtribe',
        'genus',
        'cnameGenus',
        'genusKey',
        'subgenus',
        'cnamesubgenus',
        'species',
        'cnameSpecies',
        'speciesKey',
        'subspecies',
        'cnamesubspecies',
        'variety',
        'cnamevariety',
        'iucnRedListCategory',
        'statuskepunahan',
    ];

    // Contoh metode untuk mendapatkan nama lengkap
    public function getFullNameAttribute()
    {
        return "{$this->cnameGenus} {$this->cnameSpecies}";
    }
}
