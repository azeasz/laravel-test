<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaSimilarIdentification extends Model
{
    protected $fillable = [
        'taxa_id',
        'similar_taxa_id',
        'confusion_count',
        'similarity_type',
        'notes'
    ];

    public function taxa()
    {
        return $this->belongsTo(Taxa::class, 'taxa_id');
    }

    public function similarTaxa()
    {
        return $this->belongsTo(Taxa::class, 'similar_taxa_id');
    }
}
