<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FobiChecklistTaxa extends Model
{
    use HasFactory;

    protected $table = 'fobi_checklist_taxas';

    protected $fillable = [
        'taxa_id',
        'user_id',
        'media_id',
        'scientific_name',
        'original_scientific_name',
        'kingdom',
        'phylum',
        'class',
        'order',
        'family',
        'genus',
        'species',
        'latitude',
        'longitude',
        'observation_details',
        'status',
        'iucn_status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'observation_details' => 'array',
    ];

    // Ubah relasi menjadi hasMany karena satu taxa bisa memiliki banyak media
    public function medias()
    {
        return $this->hasMany(FobiChecklistMedia::class, 'checklist_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }
    public function qualityAssessment()
    {
        return $this->hasOne(TaxaQualityAssessment::class, 'taxa_id', 'id');
    }

    public function identifications()
    {
        return $this->hasMany(TaxaIdentification::class, 'checklist_id', 'id');
    }

    public function locationVerifications()
    {
        return $this->hasMany(TaxaLocationVerification::class, 'checklist_id', 'id');
    }

    public function wildStatusVotes()
    {
        return $this->hasMany(TaxaWildStatusVote::class, 'checklist_id', 'id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(FobiUser::class, 'updated_by');
    }
}
