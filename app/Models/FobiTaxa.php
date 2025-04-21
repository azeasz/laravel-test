<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FobiTaxa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fobi_taxa';

    protected $fillable = [
        'scientificName',
        'taxonRank',
        'kingdom',
        'phylum',
        'class',
        'order',
        'family',
        'genus',
        'species',
        'taxonomicStatus',
        'taxa_type'
    ];
}
