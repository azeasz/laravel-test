<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WildStatusVote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wild_status_votes';

    protected $fillable = [
        'observation_id',
        'observation_type',
        'user_id',
        'is_wild',
        'reason'
    ];

    protected $casts = [
        'is_wild' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who voted.
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
     * Get the quality assessment for this vote.
     */
    public function qualityAssessment()
    {
        return match($this->observation_type) {
            'kupunesia' => $this->belongsTo(DataQualityAssessmentKupnes::class, 'observation_id', 'observation_id'),
            default => $this->belongsTo(DataQualityAssessment::class, 'observation_id', 'observation_id'),
        };
    }

    /**
     * Scope a query to only include wild votes.
     */
    public function scopeWild($query)
    {
        return $query->where('is_wild', true);
    }

    /**
     * Scope a query to only include captive votes.
     */
    public function scopeCaptive($query)
    {
        return $query->where('is_wild', false);
    }

    /**
     * Scope a query to only include votes for a specific observation type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('observation_type', $type);
    }

    /**
     * Get the wild status percentage for an observation.
     */
    public static function getWildPercentage($observationId, $observationType)
    {
        $votes = self::where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->get();

        if ($votes->isEmpty()) {
            return 0;
        }

        $wildVotes = $votes->where('is_wild', true)->count();
        return ($wildVotes / $votes->count()) * 100;
    }

    /**
     * Check if there's a consensus on wild status.
     */
    public function hasConsensus()
    {
        $votes = $this->observation
            ->wildStatusVotes()
            ->get();

        if ($votes->count() < 3) {
            return false;
        }

        $wildPercentage = ($votes->where('is_wild', true)->count() / $votes->count()) * 100;
        return $wildPercentage >= 80 || $wildPercentage <= 20;
    }

    /**
     * Get the consensus status.
     */
    public function getConsensusStatusAttribute()
    {
        if (!$this->hasConsensus()) {
            return 'pending';
        }

        $wildPercentage = self::getWildPercentage($this->observation_id, $this->observation_type);
        return $wildPercentage >= 80 ? 'wild' : 'captive';
    }

    /**
     * Get the vote status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->is_wild ? 'Wild/Naturalized' : 'Captive/Cultivated';
    }

    /**
     * Get the formatted reason.
     */
    public function getFormattedReasonAttribute()
    {
        return $this->reason ? nl2br(e($this->reason)) : 'Tidak ada alasan yang diberikan';
    }

    /**
     * Get the vote date in local format.
     */
    public function getVoteDateAttribute()
    {
        return $this->created_at->format('d F Y H:i');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika vote dibuat atau diupdate
        static::saved(function ($vote) {
            // Update quality assessment
            $assessment = $vote->qualityAssessment;
            if ($assessment) {
                $wildPercentage = self::getWildPercentage(
                    $vote->observation_id,
                    $vote->observation_type
                );

                // Update status wild jika mencapai threshold
                if ($vote->observation->wildStatusVotes()->count() >= 10) {
                    $assessment->update([
                        'is_wild' => $wildPercentage >= 80
                    ]);

                    // Update grade jika diperlukan
                    if ($assessment->grade === 'research grade' && $wildPercentage < 80) {
                        $assessment->update(['grade' => 'casual']);
                    }
                }
            }
        });

        // Prevent duplicate votes
        static::saving(function ($vote) {
            $existingVote = self::where('observation_id', $vote->observation_id)
                ->where('observation_type', $vote->observation_type)
                ->where('user_id', $vote->user_id)
                ->where('id', '!=', $vote->id)
                ->exists();

            if ($existingVote) {
                throw new \Exception('User sudah memberikan vote untuk observasi ini.');
            }
        });
    }

    /**
     * Get the total votes count for an observation.
     */
    public static function getTotalVotes($observationId, $observationType)
    {
        return self::where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->count();
    }

    /**
     * Check if user has voted.
     */
    public static function hasUserVoted($userId, $observationId, $observationType)
    {
        return self::where('user_id', $userId)
            ->where('observation_id', $observationId)
            ->where('observation_type', $observationType)
            ->exists();
    }
}
