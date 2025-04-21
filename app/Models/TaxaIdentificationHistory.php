<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxaIdentificationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'taxa_id',
        'previous_taxa_id',
        'user_id',
        'action_type',
        'scientific_name',
        'taxon_key',
        'accepted_scientific_name',
        'taxon_rank',
        'taxonomic_status',
        'current_taxonomy',
        'previous_taxonomy',
        'reason'
    ];

    protected $casts = [
        'current_taxonomy' => 'array',
        'previous_taxonomy' => 'array'
    ];
}
