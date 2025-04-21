<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KupunesiaQualityAssessmentController extends Controller
{
    public function evaluateQuality($observationId)
    {
        try {
            DB::beginTransaction();

            // Hitung total identifikasi dan agreement
            $stats = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->whereNull('deleted_at')
                ->selectRaw('
                    COUNT(*) as total_identifications,
                    SUM(CASE WHEN is_agreed = 1 THEN 1 ELSE 0 END) as agreement_count
                ')
                ->first();

            // Ambil atau buat quality assessment
            $assessment = DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->first();

            if (!$assessment) {
                DB::table('data_quality_assessments_kupnes')->insert([
                    'observation_id' => $observationId,
                    'grade' => 'needs ID',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $assessment = DB::table('data_quality_assessments_kupnes')
                    ->where('observation_id', $observationId)
                    ->first();
            }

            // Evaluasi grade
            $newGrade = $this->determineGrade(
                $assessment,
                $stats->agreement_count,
                $stats->total_identifications
            );

            // Update grade
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'grade' => $newGrade,
                    'updated_at' => now()
                ]);

            // Tambahkan IUCN status ke response
            $response = [
                'success' => true,
                'grade' => $newGrade,
                'iucn_status' => null
            ];

            if ($newGrade === 'research grade') {
                // Ambil IUCN status dari tabel taxas dengan nama kolom yang benar
                $iucnStatus = DB::table('fobi_checklist_faunasv2 as cf')
                    ->join('faunas_kupnes as f', 'cf.fauna_id', '=', 'f.id')
                    ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                    ->where('cf.checklist_id', $observationId)
                    ->value('t.iucn_red_list_category');

                $response['iucn_status'] = $iucnStatus;
            }

            DB::commit();
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in evaluateQuality: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error evaluating quality'
            ], 500);
        }
    }

    private function determineGrade($assessment, $agreementCount, $totalIdentifications)
    {
        // Hitung identifikasi aktif dan persetujuan
        $stats = DB::table('kupunesia_identifications')
            ->where('observation_id', $assessment->observation_id)
            ->whereNull('deleted_at')
            ->where('is_withdrawn', 0)  // Gunakan 0/1 untuk tinyint
            ->selectRaw('
                COUNT(*) as total_active_identifications,
                SUM(CASE WHEN is_agreed = 1 OR user_agreed = 1 THEN 1 ELSE 0 END) as total_agreements,
                COUNT(DISTINCT taxon_id) as unique_taxa
            ')
            ->first();

        $totalActiveIdentifications = $stats->total_active_identifications;
        $totalAgreements = $stats->total_agreements;

        // Debug log
        Log::info('Grade Calculation Stats:', [
            'observation_id' => $assessment->observation_id,
            'total_active' => $totalActiveIdentifications,
            'total_agreements' => $totalAgreements,
            'has_media' => $assessment->has_media,
            'has_date' => $assessment->has_date,
            'has_location' => $assessment->has_location
        ]);

        // Cek community taxon
        $communityTaxon = $this->determineCommunityTaxon($assessment->observation_id);

        // Research Grade
        if ($assessment->has_media &&
            $assessment->has_date &&
            $assessment->has_location &&
            $totalActiveIdentifications >= 2 &&
            $communityTaxon &&
            $totalAgreements >= 2) {
            return 'research grade';
        }

        // Low Quality ID
        if ($assessment->has_media && $totalActiveIdentifications > 0) {
            return 'low quality ID';
        }

        // Needs ID
        if ($assessment->has_media) {
            return 'needs ID';
        }

        return 'casual';
    }

    private function allIdentificationsWithdrawn($observationId)
    {
        $activeIdentifications = DB::table('kupunesia_identifications')
            ->where('observation_id', $observationId)
            ->whereNull('deleted_at')
            ->where('is_withdrawn', false)
            ->count();

        return $activeIdentifications === 0;
    }

    private function determineCommunityTaxon($observationId)
    {
        try {
            // Ambil semua identifikasi aktif dan persetujuannya
            $identifications = DB::table('kupunesia_identifications as ki')
                ->select(
                    'ki.taxon_id',
                    DB::raw('COUNT(*) as identification_count'),
                    DB::raw('SUM(CASE WHEN ki.is_agreed = 1 OR ki.user_agreed = 1 THEN 1 ELSE 0 END) as agreement_count')
                )
                ->where('ki.observation_id', $observationId)
                ->whereNull('ki.deleted_at')
                ->where('ki.is_withdrawn', 0)
                ->groupBy('ki.taxon_id')
                ->having('agreement_count', '>=', 2) // Minimal 2 persetujuan
                ->orderBy('agreement_count', 'desc')
                ->first();

            if (!$identifications) {
                return null;
            }

            // Ambil detail taxon yang disetujui
            $agreedTaxon = DB::table('faunas_kupnes as f')
                ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                ->where('f.id', $identifications->taxon_id)
                ->select(
                    'f.id as fauna_id',
                    't.iucn_red_list_category',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species'
                )
                ->first();

            if ($agreedTaxon) {
                return [
                    'taxon_id' => $identifications->taxon_id,
                    'fauna_id' => $agreedTaxon->fauna_id,
                    'iucn_status' => $agreedTaxon->iucn_red_list_category
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error in determineCommunityTaxon: ' . $e->getMessage());
            return null;
        }
    }

    private function calculateTaxonScore($identifications, $level)
    {
        $totalCount = $identifications->count();
        if ($totalCount === 0) return ['score' => 0, 'count' => 0, 'value' => null];

        // Hitung frekuensi setiap nilai pada level taksonomi ini
        $frequencies = $identifications->groupBy($level)
            ->map(function ($group) {
                return $group->count();
            });

        // Ambil nilai dengan frekuensi tertinggi
        $maxFreq = $frequencies->max();
        $mostCommonValue = $frequencies->filter(function ($freq) use ($maxFreq) {
            return $freq === $maxFreq;
        })->keys()->first();

        // Hitung disagreements (identifikasi yang berbeda pada level ini)
        $disagreements = $totalCount - $maxFreq;

        // Hitung ancestor disagreements (identifikasi pada level yang lebih tinggi)
        $ancestorDisagreements = $identifications->filter(function ($id) use ($level) {
            return $id->$level === null;
        })->count();

        // Hitung score menggunakan rumus iNaturalist
        $score = $maxFreq / ($maxFreq + $disagreements + $ancestorDisagreements);

        return [
            'score' => $score,
            'count' => $maxFreq,
            'value' => $mostCommonValue
        ];
    }

    private function updateChecklistTaxon($observationId)
    {
        try {
            // Ambil identifikasi dengan persetujuan terbanyak
            $mostAgreedIdentification = DB::table('kupunesia_identifications as ki')
                ->select(
                    'ki.taxon_id',
                    'f.nameLat as scientific_name',
                    't.iucn_red_list_category',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    DB::raw('COUNT(ki2.id) as agreement_count')
                )
                ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
                ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                ->leftJoin('kupunesia_identifications as ki2', function($join) {
                    $join->on('ki.id', '=', 'ki2.agrees_with_id')
                        ->where('ki2.is_agreed', '=', true);
                })
                ->where('ki.observation_id', $observationId)
                ->where('ki.is_first', true)
                ->groupBy(
                    'ki.taxon_id',
                    'f.nameLat',
                    't.iucn_red_list_category',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species'
                )
                ->first();

            if ($mostAgreedIdentification) {
                // Update checklist dengan fauna dan data taxonomi yang disetujui
                $updateData = [
                    'fauna_id' => $mostAgreedIdentification->taxon_id,
                    'scientific_name' => $mostAgreedIdentification->scientific_name,
                    'class' => $mostAgreedIdentification->class,
                    'order' => $mostAgreedIdentification->order,
                    'family' => $mostAgreedIdentification->family,
                    'genus' => $mostAgreedIdentification->genus,
                    'species' => $mostAgreedIdentification->species,
                    'agreement_count' => $mostAgreedIdentification->agreement_count
                ];

                // Cek apakah memenuhi kriteria research grade
                $assessment = DB::table('data_quality_assessments_kupnes')
                    ->where('observation_id', $observationId)
                    ->first();

                if ($assessment && $assessment->grade === 'research grade') {
                    $updateData['iucn_status'] = $mostAgreedIdentification->iucn_red_list_category;
                } else {
                    $updateData['iucn_status'] = null;
                }

                DB::table('fobi_checklists_kupnes')
                    ->where('id', $observationId)
                    ->update($updateData);

                Log::info('Checklist Kupunesia updated:', [
                    'observation_id' => $observationId,
                    'new_fauna_id' => $mostAgreedIdentification->taxon_id,
                    'agreement_count' => $mostAgreedIdentification->agreement_count,
                    'is_research_grade' => $assessment ? $assessment->grade === 'research grade' : false
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating checklist fauna: ' . $e->getMessage());
            throw $e;
        }
    }

    private function evaluateAndUpdateGrade($assessment, $agreementCount)
    {
        try {
            $checklist = DB::table('fobi_checklists_kupnes')
                ->where('id', $assessment->observation_id)
                ->first();

            $oldGrade = $assessment->grade;

            if ($this->meetsResearchGradeCriteria($assessment, $agreementCount)) {
                $assessment->grade = 'research grade';

                // Jika baru mencapai research grade, update IUCN status
                if ($oldGrade !== 'research grade') {
                    $approvedFauna = DB::table('kupunesia_identifications as ki')
                        ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
                        ->where('ki.observation_id', $assessment->observation_id)
                        ->where('ki.is_agreed', true)
                        ->select('f.iucn_red_list_category')
                        ->first();

                    if ($approvedFauna && $approvedFauna->iucn_red_list_category) {
                        DB::table('fobi_checklists_kupnes')
                            ->where('id', $assessment->observation_id)
                            ->update([
                                'iucn_status' => $approvedFauna->iucn_red_list_category
                            ]);
                    }
                }
            }
            else if ($this->meetsNeedsIdCriteria($assessment, $agreementCount)) {
                $assessment->grade = 'needs ID';
            }
            else {
                $assessment->grade = 'casual';
            }

            $assessment->save();

            Log::info('Kupunesia grade evaluation result:', [
                'observation_id' => $assessment->observation_id,
                'old_grade' => $oldGrade,
                'new_grade' => $assessment->grade,
                'agreement_count' => $agreementCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error in evaluateAndUpdateGrade:', [
                'error' => $e->getMessage(),
                'observation_id' => $assessment->observation_id ?? null
            ]);
            throw $e;
        }
    }

    public function checkInitialQuality($observationId)
    {
        try {
            $observation = DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->first();

            if (!$observation) {
                throw new \Exception('Observation not found');
            }

            // Cek media
            $hasMedia = DB::table('fobi_checklist_fauna_imgs_kupnes')
                ->where('checklist_id', $observationId)
                ->exists();

            // Cek lokasi
            $hasLocation = !empty($observation->latitude) && !empty($observation->longitude);

            // Cek tanggal
            $hasDate = !empty($observation->observed_at);

            // Update atau buat quality assessment
            DB::table('data_quality_assessments_kupnes')
                ->updateOrInsert(
                    ['observation_id' => $observationId],
                    [
                        'has_date' => $hasDate,
                        'has_location' => $hasLocation,
                        'has_media' => $hasMedia,
                        'is_wild' => true, // Default true
                        'location_accurate' => true, // Default true
                        'needs_id' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

            // Log hasil assessment
            Log::info('Initial quality check completed', [
                'observation_id' => $observationId,
                'has_date' => $hasDate,
                'has_location' => $hasLocation,
                'has_media' => $hasMedia
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error in checkInitialQuality: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateIdentificationStatus($observationId)
    {
        try {
            // Ambil semua identifikasi yang valid
            $identifications = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->whereNull('deleted_at')
                ->where('is_withdrawn', 0)
                ->get();

            $totalIdentifications = $identifications->count();
            $agreementCount = $identifications->where('is_agreed', true)->count();

            // Tentukan community ID level
            $communityLevel = $this->determineCommunityLevel($observationId);

            // Update quality assessment
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'needs_id' => $totalIdentifications < 2,
                    'community_id_level' => $communityLevel,
                    'total_identifications' => $totalIdentifications,
                    'agreement_count' => $agreementCount,
                    'updated_at' => now()
                ]);

            // Log update
            Log::info('Identification status updated', [
                'observation_id' => $observationId,
                'total_identifications' => $totalIdentifications,
                'agreement_count' => $agreementCount,
                'community_level' => $communityLevel
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error in updateIdentificationStatus: ' . $e->getMessage());
            throw $e;
        }
    }

    private function determineCommunityLevel($observationId)
    {
        try {
            // Ambil identifikasi dengan agreement terbanyak
            $topIdentification = DB::table('kupunesia_identifications as ki')
                ->select('ki.identification_level', DB::raw('COUNT(ki2.id) as agreement_count'))
                ->leftJoin('kupunesia_identifications as ki2', function($join) {
                    $join->on('ki.id', '=', 'ki2.agrees_with_id')
                        ->where('ki2.is_agreed', '=', true);
                })
                ->where('ki.observation_id', $observationId)
                ->where('ki.is_withdrawn', 0)
                ->groupBy('ki.identification_level')
                ->orderBy('agreement_count', 'desc')
                ->first();

            if ($topIdentification && $topIdentification->agreement_count >= 2) {
                return strtolower($topIdentification->identification_level);
            }

            // Jika tidak ada konsensus, ambil level tertinggi dari identifikasi pertama
            $firstIdentification = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->where('is_withdrawn', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            return $firstIdentification ? strtolower($firstIdentification->identification_level) : null;

        } catch (\Exception $e) {
            Log::error('Error in determineCommunityLevel: ' . $e->getMessage());
            return null;
        }
    }

    private function getIdentificationsWithPhotos($observationId)
    {
        return DB::table('kupunesia_identifications as ki')
            ->join('fobi_users as u', 'ki.user_id', '=', 'u.id')
            ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
            ->where('ki.observation_id', $observationId)
            ->whereNull('ki.deleted_at')
            ->where('ki.is_withdrawn', 0)
            ->select(
                'ki.*',
                'u.uname as identifier_name',
                'f.nameLat as scientific_name',
                'f.nameId as common_name',
                DB::raw("CASE WHEN ki.photo_path IS NOT NULL
                    THEN CONCAT('" . asset('storage') . "/', ki.photo_path)
                    ELSE NULL END as photo_url")
            )
            ->orderBy('ki.created_at', 'desc')
            ->get();
    }

    private function updateCommunityConsensus($observationId)
    {
        try {
            $mostAgreedIdentification = DB::table('kupunesia_identifications as ki')
                ->select(
                    'ki.taxon_id',
                    'f.nameLat as scientific_name',
                    'f.nameId as common_name',
                    't.iucn_red_list_category',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    DB::raw('COUNT(*) as agreement_count')
                )
                ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
                ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                ->where('ki.observation_id', $observationId)
                ->where('ki.is_agreed', true)
                ->whereNull('ki.deleted_at')
                ->where('ki.is_withdrawn', 0)
                ->groupBy(
                    'ki.taxon_id',
                    'f.nameLat',
                    'f.nameId',
                    't.iucn_red_list_category',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species'
                )
                ->orderBy('agreement_count', 'desc')
                ->first();

            $updateData = [
                'updated_at' => now()
            ];

            if ($mostAgreedIdentification && $mostAgreedIdentification->agreement_count >= 2) {
                $updateData = array_merge($updateData, [
                    'fauna_id' => $mostAgreedIdentification->taxon_id,
                    'scientific_name' => $mostAgreedIdentification->scientific_name,
                    'common_name' => $mostAgreedIdentification->common_name,
                    'class' => $mostAgreedIdentification->class,
                    'order' => $mostAgreedIdentification->order,
                    'family' => $mostAgreedIdentification->family,
                    'genus' => $mostAgreedIdentification->genus,
                    'species' => $mostAgreedIdentification->species,
                    'agreement_count' => $mostAgreedIdentification->agreement_count
                ]);
            } else {
                $updateData['agreement_count'] = 0;
            }

            DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->update($updateData);

            // Update quality assessment
            $this->updateIdentificationStatus($observationId);

            Log::info('Community consensus updated:', [
                'observation_id' => $observationId,
                'agreement_count' => $mostAgreedIdentification->agreement_count ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating community consensus:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function recalculateQuality($observationId)
    {
        try {
            DB::beginTransaction();

            // Update checklist taxon
            $this->updateChecklistTaxon($observationId);

            // Update community consensus
            $this->updateCommunityConsensus($observationId);

            // Get current stats
            $stats = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->whereNull('deleted_at')
                ->where('is_withdrawn', 0)
                ->selectRaw('
                    COUNT(*) as total_identifications,
                    SUM(CASE WHEN is_agreed = 1 THEN 1 ELSE 0 END) as agreement_count
                ')
                ->first();

            // Get or create assessment
            $assessment = DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->first();

            if (!$assessment) {
                $this->checkInitialQuality($observationId);
                $assessment = DB::table('data_quality_assessments_kupnes')
                    ->where('observation_id', $observationId)
                    ->first();
            }

            // Evaluate and update grade
            $this->evaluateAndUpdateGrade($assessment, $stats->agreement_count);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Quality recalculated successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recalculating quality:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function getQualityStats($observationId)
    {
        try {
            $assessment = DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->first();

            $identifications = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->whereNull('deleted_at')
                ->where('is_withdrawn', 0)
                ->selectRaw('
                    COUNT(*) as total_identifications,
                    SUM(CASE WHEN is_agreed = 1 THEN 1 ELSE 0 END) as agreement_count
                ')
                ->first();

            return [
                'grade' => $assessment->grade ?? 'needs ID',
                'has_date' => $assessment->has_date ?? false,
                'has_location' => $assessment->has_location ?? false,
                'has_media' => $assessment->has_media ?? false,
                'is_wild' => $assessment->is_wild ?? true,
                'location_accurate' => $assessment->location_accurate ?? true,
                'needs_id' => $assessment->needs_id ?? true,
                'community_id_level' => $assessment->community_id_level,
                'total_identifications' => $identifications->total_identifications ?? 0,
                'agreement_count' => $identifications->agreement_count ?? 0
            ];

        } catch (\Exception $e) {
            Log::error('Error getting quality stats:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function verifyLocation($observationId)
    {
        try {
            $observation = DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->first();

            if (!$observation) {
                throw new \Exception('Observation not found');
            }

            // Update quality assessment
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'location_accurate' => true,
                    'updated_at' => now()
                ]);

            // Recalculate quality
            $this->recalculateQuality($observationId);

            return [
                'success' => true,
                'message' => 'Location verified successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Error verifying location:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function markAsWild($observationId)
    {
        try {
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'is_wild' => true,
                    'updated_at' => now()
                ]);

            $this->recalculateQuality($observationId);

            return [
                'success' => true,
                'message' => 'Marked as wild successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Error marking as wild:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function markAsCaptive($observationId)
    {
        try {
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'is_wild' => false,
                    'updated_at' => now()
                ]);

            $this->recalculateQuality($observationId);

            return [
                'success' => true,
                'message' => 'Marked as captive successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Error marking as captive:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function flagLocationInaccurate($observationId)
    {
        try {
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'location_accurate' => false,
                    'updated_at' => now()
                ]);

            $this->recalculateQuality($observationId);

            return [
                'success' => true,
                'message' => 'Location flagged as inaccurate'
            ];

        } catch (\Exception $e) {
            Log::error('Error flagging location:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    private function getLocationName($latitude, $longitude)
    {
        try {
            if (!$latitude || !$longitude) {
                return 'Unknown Location';
            }

            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Kupunesia Application');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $data = json_decode($response, true);

            if (isset($data['display_name'])) {
                return $data['display_name'];
            }

            return 'Unknown Location';

        } catch (\Exception $e) {
            Log::error('Error getting location name:', [
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return 'Unknown Location';
        } finally {
            if (isset($ch)) {
                curl_close($ch);
            }
        }
    }

    public function updateLocationDetails($observationId)
    {
        try {
            $observation = DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->first();

            if (!$observation || !$observation->latitude || !$observation->longitude) {
                return false;
            }

            $locationName = $this->getLocationName($observation->latitude, $observation->longitude);

            DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->update([
                    'location_name' => $locationName,
                    'updated_at' => now()
                ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating location details:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            return false;
        }
    }

    private function handleSpecialCases($observationId)
    {
        try {
            $observation = DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->first();

            if (!$observation) {
                return false;
            }

            // Cek apakah ada identifikasi yang bertentangan
            $conflictingIdentifications = DB::table('kupunesia_identifications')
                ->where('observation_id', $observationId)
                ->where('is_withdrawn', 0)
                ->whereNull('deleted_at')
                ->select('taxon_id', DB::raw('COUNT(*) as count'))
                ->groupBy('taxon_id')
                ->having('count', '>=', 2)
                ->get();

            if ($conflictingIdentifications->count() > 1) {
                // Ada lebih dari satu identifikasi dengan count >= 2
                // Set grade ke needs ID
                DB::table('data_quality_assessments_kupnes')
                    ->where('observation_id', $observationId)
                    ->update([
                        'grade' => 'needs ID',
                        'needs_id' => true,
                        'updated_at' => now()
                    ]);

                Log::info('Special case: Multiple conflicting identifications', [
                    'observation_id' => $observationId
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error in handleSpecialCases:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            return false;
        }
    }

    private function validateIdentificationLevel($observationId)
    {
        try {
            $identifications = DB::table('kupunesia_identifications as ki')
                ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
                ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                ->where('ki.observation_id', $observationId)
                ->where('ki.is_withdrawn', 0)
                ->whereNull('ki.deleted_at')
                ->select('t.species', 't.genus', 't.family', 't.order', 't.class')
                ->get();

            $lowestLevel = 'class';
            foreach ($identifications as $identification) {
                if (!empty($identification->species)) {
                    $lowestLevel = 'species';
                    break;
                } elseif (!empty($identification->genus)) {
                    $lowestLevel = 'genus';
                } elseif (!empty($identification->family)) {
                    $lowestLevel = 'family';
                } elseif (!empty($identification->order)) {
                    $lowestLevel = 'order';
                }
            }

            return $lowestLevel;
        } catch (\Exception $e) {
            Log::error('Error in validateIdentificationLevel:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            return 'class';
        }
    }

    public function resetQualityAssessment($observationId)
    {
        try {
            DB::beginTransaction();

            // Reset quality assessment
            DB::table('data_quality_assessments_kupnes')
                ->where('observation_id', $observationId)
                ->update([
                    'grade' => 'needs ID',
                    'needs_id' => true,
                    'community_id_level' => null,
                    'updated_at' => now()
                ]);

            // Reset checklist data
            DB::table('fobi_checklists_kupnes')
                ->where('id', $observationId)
                ->update([
                    'fauna_id' => null,
                    'scientific_name' => null,
                    'common_name' => null,
                    'class' => null,
                    'order' => null,
                    'family' => null,
                    'genus' => null,
                    'species' => null,
                    'iucn_status' => null,
                    'agreement_count' => 0,
                    'updated_at' => now()
                ]);

            DB::commit();

            Log::info('Quality assessment reset:', [
                'observation_id' => $observationId
            ]);

            return [
                'success' => true,
                'message' => 'Quality assessment reset successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in resetQualityAssessment:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }

    public function getIdentificationHistory($observationId)
    {
        try {
            return DB::table('kupunesia_identifications as ki')
                ->join('fobi_users as u', 'ki.user_id', '=', 'u.id')
                ->join('faunas_kupnes as f', 'ki.taxon_id', '=', 'f.id')
                ->join('taxas as t', 'f.id', '=', 't.kupnes_fauna_id')
                ->where('ki.observation_id', $observationId)
                ->whereNull('ki.deleted_at')
                ->select(
                    'ki.id',
                    'ki.created_at',
                    'u.uname as identifier_name',
                    'f.nameLat as scientific_name',
                    'f.nameId as common_name',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    'ki.is_agreed',
                    'ki.is_withdrawn',
                    'ki.comment',
                    DB::raw("CASE WHEN ki.photo_path IS NOT NULL
                        THEN CONCAT('" . asset('storage') . "/', ki.photo_path)
                        ELSE NULL END as photo_url")
                )
                ->orderBy('ki.created_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            Log::error('Error in getIdentificationHistory:', [
                'error' => $e->getMessage(),
                'observation_id' => $observationId
            ]);
            throw $e;
        }
    }
}
