<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityIdentification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'community_identifications';

    protected $fillable = [
        'observation_id',
        'observation_type',
        'user_id',
        'taxon_id',
        'identification_level',
        'notes',
        'is_valid'
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who made the identification.
     */
    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }

    /**
     * Get the taxon for this identification.
     */
    public function taxon()
    {
        return $this->belongsTo(FobiTaxa::class, 'taxon_id');
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
     * Get the quality assessment for this identification.
     */
    public function qualityAssessment()
    {
        return match($this->observation_type) {
            'kupunesia' => $this->belongsTo(DataQualityAssessmentKupnes::class, 'observation_id', 'observation_id'),
            default => $this->belongsTo(DataQualityAssessment::class, 'observation_id', 'observation_id'),
        };
    }

    /**
     * Scope a query to only include valid identifications.
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope a query to only include identifications for a specific observation type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('observation_type', $type);
    }

    /**
     * Scope a query to only include species-level identifications.
     */
    public function scopeSpeciesLevel($query)
    {
        return $query->where('identification_level', 'species');
    }

    /**
     * Check if the identification is at species level.
     */
    public function isSpeciesLevel()
    {
        return $this->identification_level === 'species';
    }

    /**
     * Check if the identification agrees with the current community consensus.
     */
    public function agreesWithConsensus()
    {
        $consensus = $this->observation
            ->communityIdentifications()
            ->valid()
            ->where('identification_level', $this->identification_level)
            ->get();

        if ($consensus->isEmpty()) {
            return false;
        }

        $agreementCount = $consensus->where('taxon_id', $this->taxon_id)->count();
        return ($agreementCount / $consensus->count()) > (2/3);
    }

    /**
     * Get the taxonomic hierarchy for this identification.
     */
    public function getTaxonomicHierarchyAttribute()
    {
        return $this->taxon ? [
            'kingdom' => $this->taxon->kingdom,
            'phylum' => $this->taxon->phylum,
            'class' => $this->taxon->class,
            'order' => $this->taxon->order,
            'family' => $this->taxon->family,
            'genus' => $this->taxon->genus,
            'species' => $this->taxon->species
        ] : null;
    }

    /**
     * Get the formatted identification level.
     */
    public function getFormattedLevelAttribute()
    {
        return ucfirst($this->identification_level);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika identifikasi dibuat atau diupdate
        static::saved(function ($identification) {
            // Update quality assessment
            $assessment = $identification->qualityAssessment;
            if ($assessment) {
                // Hitung jumlah identifikasi yang valid
                $validIdentifications = $identification->observation
                    ->communityIdentifications()
                    ->valid()
                    ->count();

                // Update community_id_level jika mencapai threshold
                if ($validIdentifications >= 3) {
                    $consensusLevel = $identification->observation
                        ->communityIdentifications()
                        ->valid()
                        ->groupBy('identification_level')
                        ->map(function ($group) {
                            return $group->count();
                        })
                        ->sortDesc()
                        ->keys()
                        ->first();

                    $assessment->update([
                        'community_id_level' => $consensusLevel,
                        'grade' => $consensusLevel === 'species' ? 'research grade' : 'needs ID'
                    ]);
                }
            }
        });
    }
}
