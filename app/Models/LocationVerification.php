<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'location_verifications';

    protected $fillable = [
        'observation_id',
        'observation_type',
        'user_id',
        'is_accurate',
        'reason'
    ];

    protected $casts = [
        'is_accurate' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who made the verification.
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
     * Scope a query to only include accurate locations.
     */
    public function scopeAccurate($query)
    {
        return $query->where('is_accurate', true);
    }

    /**
     * Scope a query to only include inaccurate locations.
     */
    public function scopeInaccurate($query)
    {
        return $query->where('is_accurate', false);
    }

    /**
     * Scope a query to only include verifications for a specific observation type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('observation_type', $type);
    }

    /**
     * Get the accuracy percentage for an observation.
     */
    public static function getAccuracyPercentage($observationId, $observationType)
    {
        $verifications = self::where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->get();

        if ($verifications->isEmpty()) {
            return 0;
        }

        $accurateCount = $verifications->where('is_accurate', true)->count();
        return ($accurateCount / $verifications->count()) * 100;
    }

    /**
     * Check if the location has consensus on accuracy.
     */
    public function hasConsensus()
    {
        $verifications = $this->observation
            ->locationVerifications()
            ->get();

        if ($verifications->count() < 3) {
            return false;
        }

        $accuratePercentage = ($verifications->where('is_accurate', true)->count() / $verifications->count()) * 100;
        return $accuratePercentage >= 66.67 || $accuratePercentage <= 33.33;
    }

    /**
     * Get the consensus status.
     */
    public function getConsensusStatusAttribute()
    {
        if (!$this->hasConsensus()) {
            return 'pending';
        }

        $accuratePercentage = self::getAccuracyPercentage($this->observation_id, $this->observation_type);
        return $accuratePercentage >= 66.67 ? 'accurate' : 'inaccurate';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika verifikasi lokasi dibuat atau diupdate
        static::saved(function ($verification) {
            // Update quality assessment
            $assessment = $verification->qualityAssessment;
            if ($assessment) {
                $accuracyPercentage = self::getAccuracyPercentage(
                    $verification->observation_id,
                    $verification->observation_type
                );

                $assessment->update([
                    'location_accurate' => $accuracyPercentage >= 66.67
                ]);

                // Update grade jika diperlukan
                if ($assessment->grade === 'research grade' && $accuracyPercentage < 66.67) {
                    $assessment->update(['grade' => 'needs ID']);
                }
            }
        });
    }

    /**
     * Get the formatted reason.
     */
    public function getFormattedReasonAttribute()
    {
        return $this->reason ? nl2br(e($this->reason)) : 'Tidak ada alasan yang diberikan';
    }

    /**
     * Get the verification status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->is_accurate ? 'Lokasi Akurat' : 'Lokasi Tidak Akurat';
    }

    /**
     * Get the verification date in local format.
     */
    public function getVerificationDateAttribute()
    {
        return $this->created_at->format('d F Y H:i');
    }
}
