<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataQualityAssessmentKupnes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_quality_assessments_kupnes';

    protected $fillable = [
        'observation_id',
        'grade',
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
        'needs_id' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the observation that owns the assessment.
     */
    public function observation()
    {
        return $this->belongsTo(FobiChecklistKupnes::class, 'observation_id');
    }

    /**
     * Get the fauna associated with the assessment.
     */
    /**
     * Get the community identifications for this assessment.
     */
    public function communityIdentifications()
    {
        return $this->hasMany(CommunityIdentification::class, 'observation_id', 'observation_id')
            ->where('observation_type', 'kupunesia');
    }

    /**
     * Get the location verifications for this assessment.
     */
    public function locationVerifications()
    {
        return $this->hasMany(LocationVerification::class, 'observation_id', 'observation_id')
            ->where('observation_type', 'kupunesia');
    }

    /**
     * Get the wild status votes for this assessment.
     */
    public function wildStatusVotes()
    {
        return $this->hasMany(WildStatusVote::class, 'observation_id', 'observation_id')
            ->where('observation_type', 'kupunesia');
    }

    /**
     * Get the evidence verifications for this assessment.
     */
    public function evidenceVerifications()
    {
        return $this->hasMany(EvidenceVerification::class, 'observation_id', 'observation_id')
            ->where('observation_type', 'kupunesia');
    }

    /**
     * Scope a query to only include assessments with specific grade.
     */
    public function scopeWithGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope a query to only include assessments that need ID.
     */
    public function scopeNeedsId($query)
    {
        return $query->where('needs_id', true);
    }

    /**
     * Scope a query to only include research grade assessments.
     */
    public function scopeResearchGrade($query)
    {
        return $query->where('grade', 'research grade');
    }

    /**
     * Check if the assessment is research grade.
     */
    public function isResearchGrade()
    {
        return $this->grade === 'research grade';
    }

    /**
     * Check if the assessment needs ID.
     */
    public function needsIdentification()
    {
        return $this->needs_id;
    }

    /**
     * Get the latest community identification.
     */
    public function getLatestIdentificationAttribute()
    {
        return $this->communityIdentifications()
            ->latest()
            ->first();
    }

    /**
     * Get the percentage of wild status votes.
     */
    public function getWildStatusPercentageAttribute()
    {
        $votes = $this->wildStatusVotes;
        if ($votes->isEmpty()) {
            return 0;
        }

        $wildVotes = $votes->where('is_wild', true)->count();
        return ($wildVotes / $votes->count()) * 100;
    }

    /**
     * Get the location accuracy percentage.
     */
    public function getLocationAccuracyPercentageAttribute()
    {
        $verifications = $this->locationVerifications;
        if ($verifications->isEmpty()) {
            return 0;
        }

        $accurateCount = $verifications->where('is_accurate', true)->count();
        return ($accurateCount / $verifications->count()) * 100;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika assessment dihapus, hapus juga verifikasi terkait
        static::deleting(function ($assessment) {
            $assessment->communityIdentifications()->delete();
            $assessment->locationVerifications()->delete();
            $assessment->wildStatusVotes()->delete();
            $assessment->evidenceVerifications()->delete();
        });
    }
}
