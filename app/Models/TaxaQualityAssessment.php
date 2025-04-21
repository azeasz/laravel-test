<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaQualityAssessment extends Model
{
    protected $table = 'taxa_quality_assessments';

    protected $fillable = [
        'taxa_id',
        'taxon_id',
        'grade',
        'can_be_improved',
        'has_date',
        'has_location',
        'has_media',
        'is_wild',
        'location_accurate',
        'recent_evidence',
        'related_evidence',
        'community_id_level'  // Tambahkan ini jika belum ada
    ];
    protected $casts = [
        'can_be_improved' => 'boolean',
        'has_date' => 'boolean',
        'has_location' => 'boolean',
        'has_media' => 'boolean',
        'is_wild' => 'boolean',
        'location_accurate' => 'boolean',
        'recent_evidence' => 'boolean',
        'related_evidence' => 'boolean'
    ];

    public function taxa()
    {
        return $this->belongsTo(FobiChecklistTaxa::class, 'taxa_id');
    }
    public function comments()
{
        return $this->hasMany(ChecklistComment::class, 'checklist_id');
    }

    public function taxon()
    {
        return $this->belongsTo(Taxa::class, 'taxon_id');
    }
}
