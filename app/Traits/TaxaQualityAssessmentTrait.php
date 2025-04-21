<?php

namespace App\Traits;

trait TaxaQualityAssessmentTrait
{
    protected function assessTaxaQuality($taxa)
    {
        $assessment = [
            'grade' => 'casual',
            'has_date' => !empty($taxa->created_at),
            'has_location' => !empty($taxa->location),
            'has_media' => $this->hasValidMedia($taxa),
            'is_wild' => true,
            'location_accurate' => $this->isLocationAccurate($taxa),
            'recent_evidence' => $this->isRecentEvidence($taxa),
            'related_evidence' => true,
            'needs_id' => false,
            'community_id_level' => null
        ];

        if ($assessment['has_date'] &&
            $assessment['has_location'] &&
            $assessment['has_media']) {
            $assessment['needs_id'] = true;
            $assessment['grade'] = 'needs ID';
        }

        if ($assessment['needs_id'] &&
            $this->communityAgreesOnSpecies($taxa) &&
            $assessment['is_wild'] &&
            $assessment['location_accurate'] &&
            $assessment['recent_evidence'] &&
            $assessment['related_evidence']) {
            $assessment['grade'] = 'research grade';
        }

        return $assessment;
    }

    private function hasValidMedia($taxa)
    {
        return !empty($taxa->media);
    }

    private function isLocationAccurate($taxa)
    {
        return true;
    }

    private function isRecentEvidence($taxa)
    {
        $hundredYearsAgo = now()->subYears(100);
        return $taxa->created_at > $hundredYearsAgo;
    }

    private function communityAgreesOnSpecies($taxa)
    {
        return false;
    }
}
