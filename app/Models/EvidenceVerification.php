<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvidenceVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evidence_verifications';

    protected $fillable = [
        'observation_id',
        'observation_type',
        'user_id',
        'is_valid_evidence',
        'is_recent',
        'is_related',
        'notes'
    ];

    protected $casts = [
        'is_valid_evidence' => 'boolean',
        'is_recent' => 'boolean',
        'is_related' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who verified.
     */
    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }

    /**
     * Get the observation based on type.
     */
    public function observation()
    {
        return match($this->observation_type) {
            'kupunesia' => $this->belongsTo(FobiChecklistKupnes::class, 'observation_id'),
            'burungnesia' => $this->belongsTo(FobiChecklist::class, 'observation_id'),
            default => $this->belongsTo(FobiChecklist::class, 'observation_id'),
        };
    }

    /**
     * Get the quality assessment for this verification.
     */
    public function qualityAssessment()
    {
        return match($this->observation_type) {
            'kupunesia' => $this->belongsTo(DataQualityAssessmentKupnes::class, 'observation_id', 'observation_id'),
            default => $this->belongsTo(DataQualityAssessment::class, 'observation_id', 'observation_id'),
        };
    }

    /**
     * Scope a query to only include valid evidence.
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid_evidence', true);
    }

    /**
     * Scope a query to only include recent evidence.
     */
    public function scopeRecent($query)
    {
        return $query->where('is_recent', true);
    }

    /**
     * Scope a query to only include related evidence.
     */
    public function scopeRelated($query)
    {
        return $query->where('is_related', true);
    }

    /**
     * Scope a query to only include verifications for a specific observation type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('observation_type', $type);
    }

    /**
     * Get the evidence validity percentage.
     */
    public static function getValidityPercentage($observationId, $observationType)
    {
        $verifications = self::where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->get();

        if ($verifications->isEmpty()) {
            return [
                'valid' => 0,
                'recent' => 0,
                'related' => 0
            ];
        }

        return [
            'valid' => ($verifications->where('is_valid_evidence', true)->count() / $verifications->count()) * 100,
            'recent' => ($verifications->where('is_recent', true)->count() / $verifications->count()) * 100,
            'related' => ($verifications->where('is_related', true)->count() / $verifications->count()) * 100
        ];
    }

    /**
     * Check if there's a consensus on evidence validity.
     */
    public function hasConsensus()
    {
        $verifications = $this->observation
            ->evidenceVerifications()
            ->get();

        if ($verifications->count() < 3) {
            return false;
        }

        $percentages = self::getValidityPercentage($this->observation_id, $this->observation_type);

        return $percentages['valid'] >= 66.67 &&
               $percentages['recent'] >= 66.67 &&
               $percentages['related'] >= 66.67;
    }

    /**
     * Get the verification status summary.
     */
    public function getStatusSummaryAttribute()
    {
        $status = [];

        if ($this->is_valid_evidence) $status[] = 'Valid';
        if ($this->is_recent) $status[] = 'Recent';
        if ($this->is_related) $status[] = 'Related';

        return empty($status) ? 'Tidak Valid' : implode(', ', $status);
    }

    /**
     * Get the formatted notes.
     */
    public function getFormattedNotesAttribute()
    {
        return $this->notes ? nl2br(e($this->notes)) : 'Tidak ada catatan';
    }

    /**
     * Get the verification date in local format.
     */
    public function getVerificationDateAttribute()
    {
        return $this->created_at->format('d F Y H:i');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika verifikasi dibuat atau diupdate
        static::saved(function ($verification) {
            // Update quality assessment
            $assessment = $verification->qualityAssessment;
            if ($assessment) {
                $percentages = self::getValidityPercentage(
                    $verification->observation_id,
                    $verification->observation_type
                );

                // Update status evidence jika mencapai threshold
                if ($verification->observation->evidenceVerifications()->count() >= 3) {
                    $assessment->update([
                        'recent_evidence' => $percentages['recent'] >= 66.67,
                        'related_evidence' => $percentages['related'] >= 66.67
                    ]);

                    // Update grade jika diperlukan
                    if ($assessment->grade === 'research grade' &&
                        ($percentages['valid'] < 66.67 ||
                         $percentages['recent'] < 66.67 ||
                         $percentages['related'] < 66.67)) {
                        $assessment->update(['grade' => 'casual']);
                    }
                }
            }
        });

        // Prevent duplicate verifications
        static::saving(function ($verification) {
            $existingVerification = self::where('observation_id', $verification->observation_id)
                ->where('observation_type', $verification->observation_type)
                ->where('user_id', $verification->user_id)
                ->where('id', '!=', $verification->id)
                ->exists();

            if ($existingVerification) {
                throw new \Exception('User sudah memverifikasi bukti untuk observasi ini.');
            }
        });
    }

    /**
     * Get verification statistics for an observation.
     */
    public static function getStats($observationId, $observationType)
    {
        $verifications = self::where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->get();

        return [
            'total' => $verifications->count(),
            'valid' => $verifications->where('is_valid_evidence', true)->count(),
            'recent' => $verifications->where('is_recent', true)->count(),
            'related' => $verifications->where('is_related', true)->count(),
            'percentages' => self::getValidityPercentage($observationId, $observationType)
        ];
    }

    /**
     * Check if user has verified.
     */
    public static function hasUserVerified($userId, $observationId, $observationType)
    {
        return self::where('user_id', $userId)
            ->where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->exists();
    }
}
