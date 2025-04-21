<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataQualityAssessment extends Model
{
    protected $table = 'data_quality_assessments';

    protected $fillable = [
        'observation_id',
        'grade', // casual, needs ID, research grade
        'has_date',
        'has_location',
        'has_media',
        'is_wild',
        'has_evidence',
        'community_agrees',
        'location_accurate',
        'recent_evidence',
        'related_evidence',
        'needs_id',
        'community_id_level'
    ];

    protected $casts = [
        'has_date' => 'boolean',
        'has_location' => 'boolean',
        'has_media' => 'boolean',
        'is_wild' => 'boolean',
        'has_evidence' => 'boolean',
        'community_agrees' => 'boolean',
        'location_accurate' => 'boolean',
        'recent_evidence' => 'boolean',
        'related_evidence' => 'boolean',
        'needs_id' => 'boolean'
    ];
}
