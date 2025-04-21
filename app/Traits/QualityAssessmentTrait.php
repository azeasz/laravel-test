<?php

namespace App\Traits;

trait QualityAssessmentTrait
{
    protected function assessQuality($observation)
    {
        $assessment = [
            'grade' => 'casual',
            'has_date' => !empty($observation->tgl_pengamatan),
            'has_location' => !empty($observation->latitude) && !empty($observation->longitude),
            'has_media' => $this->hasValidMedia($observation),
            'is_wild' => true, // Default true, akan diupdate oleh sistem voting
            'location_accurate' => $this->isLocationAccurate($observation),
            'recent_evidence' => $this->isRecentEvidence($observation),
            'related_evidence' => true, // Default true, akan diupdate berdasarkan review
            'needs_id' => false,
            'community_id_level' => null
        ];

        // Check if needs ID
        if ($assessment['has_date'] &&
            $assessment['has_location'] &&
            $assessment['has_media'] &&
            $this->isNotHuman($observation)) {
            $assessment['needs_id'] = true;
            $assessment['grade'] = 'needs ID';
        }

        // Check if research grade
        if ($assessment['needs_id'] &&
            $this->communityAgreesOnSpecies($observation) &&
            $assessment['is_wild'] &&
            $assessment['location_accurate'] &&
            $assessment['recent_evidence'] &&
            $assessment['related_evidence']) {
            $assessment['grade'] = 'research grade';
        }

        return $assessment;
    }

    private function hasValidMedia($observation)
    {
        return !empty($observation->images) || !empty($observation->sounds);
    }

    private function isLocationAccurate($observation)
    {
        // Implementasi logika untuk mengecek akurasi lokasi
        // Contoh: cek koordinat masuk akal untuk habitat spesies tersebut
        return true; // Default implementation
    }

    private function isRecentEvidence($observation)
    {
        // Cek apakah pengamatan dalam 100 tahun terakhir
        if (empty($observation->tgl_pengamatan)) {
            return false;
        }

        $observationDate = strtotime($observation->tgl_pengamatan);
        $hundredYearsAgo = strtotime('-100 years');

        return $observationDate > $hundredYearsAgo;
    }

    private function isNotHuman($observation)
    {
        // Implementasi logika untuk mengecek apakah subjek bukan manusia
        // Bisa menggunakan AI image recognition atau kategori taksonomi
        return true; // Default implementation
    }

    private function communityAgreesOnSpecies($observation)
    {
        // Implementasi logika untuk mengecek kesepakatan komunitas
        // Contoh: 2/3 dari identifier setuju dengan takson
        return false; // Default implementation
    }
}
