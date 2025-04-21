<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TaxaIdentificationHistory;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\TaxaSimilarIdentification;

class ChecklistQualityAssessmentController extends Controller
{
    private function getChecklistTable($source)
    {
        return match($source) {
            'burungnesia' => 'fobi_checklists',
            'kupunesia' => 'fobi_checklists_kupnes',
            default => 'fobi_checklist_taxas'
        };
    }

    private function getAssessmentTable($source)
    {
        return match($source) {
            'burungnesia' => 'data_quality_assessments',
            'kupunesia' => 'data_quality_assessments_kupnes',
            default => 'taxa_quality_assessments'
        };
    }

    private function checkHasMedia($id, $source)
    {
        if ($source === 'burungnesia') {
            return DB::table('fobi_checklist_fauna_imgs')
                ->where('checklist_id', $id)
                ->exists() ||
                DB::table('fobi_checklist_sounds')
                    ->where('checklist_id', $id)
                    ->exists();
        } elseif ($source === 'kupunesia') {
            return DB::table('fobi_checklist_fauna_imgs_kupnes')
                ->where('checklist_id', $id)
                ->exists();
        } else {
            return DB::table('fobi_checklist_media')
                ->where('checklist_id', $id)
                ->exists();
        }
    }

    private function getActualId($id, $source)
    {
        if ($source === 'burungnesia' && str_starts_with($id, 'BN')) {
            return substr($id, 2);
        }
        if ($source === 'kupunesia' && str_starts_with($id, 'KP')) {
            return substr($id, 2);
        }
        return $id;
    }

    public function getAssessmentConfig($source)
    {
        return match($source) {
            'burungnesia' => [
                'table' => 'data_quality_assessments',
                'id_column' => 'observation_id',
                'fauna_column' => 'fauna_id'
            ],
            'kupunesia' => [
                'table' => 'data_quality_assessments_kupnes',
                'id_column' => 'observation_id',
                'fauna_column' => 'fauna_id'
            ],
            default => [
                'table' => 'taxa_quality_assessments',
                'id_column' => 'taxa_id',
                'fauna_column' => 'taxon_id'
            ]
        };
    }

    private function determineGrade($totalIdentifications, $agreementCount, $hasMedia, $hasLocation, $hasDate, $actualId)
    {
        // Hitung identifikasi aktif dan persetujuannya
        $identificationStats = DB::table('taxa_identifications as ti1')
            ->select([
                'ti1.taxon_id',
                'ti1.id as identification_id',
                DB::raw('COUNT(DISTINCT ti1.user_id) as identifier_count'),
                DB::raw('CASE WHEN COUNT(DISTINCT CASE
                    WHEN ti2.agrees_with_id = ti1.id AND (ti2.is_withdrawn = false OR ti2.is_withdrawn IS NULL)
                    THEN ti2.user_id END) = 0
                    THEN NULL
                    ELSE COUNT(DISTINCT CASE
                        WHEN ti2.agrees_with_id = ti1.id AND (ti2.is_withdrawn = false OR ti2.is_withdrawn IS NULL)
                        THEN ti2.user_id END)
                    END as agreement_count')
            ])
            ->leftJoin('taxa_identifications as ti2', 'ti1.id', '=', 'ti2.agrees_with_id')
            ->where(function($query) use ($actualId) {
                $query->where('ti1.checklist_id', $actualId)
                      ->orWhere('ti1.burnes_checklist_id', $actualId)
                      ->orWhere('ti1.kupnes_checklist_id', $actualId);
            })
            ->where(function($query) {
                $query->where('ti1.is_withdrawn', false)
                      ->orWhereNull('ti1.is_withdrawn');
            })
            ->groupBy('ti1.taxon_id', 'ti1.id')
            ->get();

        // Hitung total identifikasi aktif
        $activeIdentifications = $identificationStats->sum('identifier_count');

        Log::info('Active identifications', [
            'count' => $activeIdentifications,
            'stats' => $identificationStats
        ]);

        // Analisis statistik identifikasi
        $maxAgreements = 0;
        $taxaWithAgreements = 0;
        $taxonAgreements = [];
        $mostAgreedTaxonId = null;

        foreach ($identificationStats as $stat) {
            $taxonId = $stat->taxon_id;
            if (!isset($taxonAgreements[$taxonId])) {
                $taxonAgreements[$taxonId] = 0;
            }
            $taxonAgreements[$taxonId] += $stat->agreement_count;

            // Update max agreements dan taxon_id dengan persetujuan terbanyak
            if ($taxonAgreements[$taxonId] > $maxAgreements) {
                $maxAgreements = $taxonAgreements[$taxonId];
                $mostAgreedTaxonId = $taxonId;
            }
        }

        // Update fauna/taxa ID jika ada yang memiliki persetujuan terbanyak
        if ($mostAgreedTaxonId) {
            $this->updateChecklistTaxon($actualId, $mostAgreedTaxonId);
        }

        // Hitung jumlah taxa yang memiliki persetujuan
        $taxaWithAgreements = count(array_filter($taxonAgreements, function($count) {
            return $count > 0;
        }));

        Log::info('Agreement stats', [
            'maxAgreements' => $maxAgreements,
            'taxaWithAgreements' => $taxaWithAgreements,
            'taxonAgreements' => $taxonAgreements,
            'activeIdentifications' => $activeIdentifications
        ]);

        // Research Grade:
        // - Memiliki media, lokasi, dan tanggal
        // - Minimal 2 user berbeda setuju dengan takson yang sama
        // - Tidak ada konflik identifikasi (hanya 1 taxon dengan persetujuan)
        if ($hasMedia && $hasLocation && $hasDate &&
            $maxAgreements >= 2 &&
            $taxaWithAgreements == 1) {
            Log::info('Determined as Research Grade', [
                'conditions' => [
                    'hasMedia' => $hasMedia,
                    'hasLocation' => $hasLocation,
                    'hasDate' => $hasDate,
                    'maxAgreements' => $maxAgreements,
                    'taxaWithAgreements' => $taxaWithAgreements
                ]
            ]);
            return 'research grade';
        }

        // Needs ID:
        // - Memiliki media, lokasi, dan tanggal
        // - Belum mencapai research grade
        // - Tidak ada konflik identifikasi
        if ($hasMedia && $hasLocation && $hasDate &&
            ($maxAgreements < 2 || $activeIdentifications == 0)) {
            Log::info('Determined as Needs ID');
            return 'needs ID';
        }

        // Low Quality ID:
        // - Ada identifikasi aktif
        // - Dan ada satu persetujuan
        // - Atau ada konflik identifikasi (multiple taxa dengan persetujuan)
        if ($activeIdentifications > 0 &&
            ($maxAgreements == 1 || $taxaWithAgreements > 1)) {
            Log::info('Determined as Low Quality ID');
            return 'low quality ID';
        }

        // Default: Casual
        Log::info('Determined as Casual');
        return 'casual';
    }

    // Method untuk mendapatkan status quality assessment
    public function getQualityAssessment($id)
    {
        try {
            $source = request()->query('source', $this->determineSource($id));
            $actualId = $this->getActualId($id, $source);
            $config = $this->getAssessmentConfig($source);

            Log::info('Starting getQualityAssessment', [
                'id' => $id,
                'source' => $source,
                'actualId' => $actualId,
                'config' => $config
            ]);

            // Get fauna/taxa ID first
            $faunaId = null;
            if ($source === 'burungnesia') {
                $faunaId = DB::table('fobi_checklist_faunasv1')
                    ->where('checklist_id', $actualId)
                    ->value('fauna_id');
            } elseif ($source === 'kupunesia') {
                $faunaId = DB::table('fobi_checklist_faunasv2')
                    ->where('checklist_id', $actualId)
                    ->value('fauna_id');
            } else {
                $faunaId = DB::table('fobi_checklist_taxas')
                    ->where('id', $actualId)
                    ->select([
                        DB::raw('COALESCE(taxa_id, original_taxa_id) as taxa_id')
                    ])
                    ->value('taxa_id');
            }

            Log::info('Fauna/Taxa ID retrieved', [
                'faunaId' => $faunaId,
                'source' => $source
            ]);

            // Get existing assessment
            $existingAssessment = DB::table($config['table'])
                ->where($config['id_column'], $actualId)
                ->get(); // Ambil semua untuk logging

            Log::info('Existing assessments found', [
                'count' => $existingAssessment->count(),
                'assessments' => $existingAssessment->toArray()
            ]);

            $assessment = DB::table($config['table'])
                ->where($config['id_column'], $actualId)
                ->when($faunaId, function($query) use ($config, $faunaId) {
                    return $query->where(function($q) use ($config, $faunaId) {
                        $q->where($config['fauna_column'], $faunaId)
                          ->orWhereNull($config['fauna_column']);
                    });
                })
                ->orderBy('id', 'desc')
                ->first();

            Log::info('Selected assessment', [
                'assessment' => $assessment,
                'query_conditions' => [
                    'id_column' => $actualId,
                    'fauna_column' => $faunaId
                ]
            ]);

            if (!$assessment) {
                Log::info('No assessment found, creating new one');

                $stats = DB::table('taxa_identifications')
                    ->where('checklist_id', $actualId)
                    ->selectRaw('COUNT(CASE WHEN agrees_with_id IS NOT NULL THEN 1 END) as agreement_count')
                    ->first();

                $hasMedia = $this->checkHasMedia($actualId, $source);
                $hasLocation = $this->checkHasLocation($actualId, $source);
                $hasDate = $this->checkHasDate($actualId, $source);

                $grade = $this->determineGrade(
                    0,
                    $stats->agreement_count ?? 0,
                    $hasMedia,
                    $hasLocation,
                    $hasDate,
                    $actualId
                );

                Log::info('Calculated assessment data', [
                    'stats' => $stats,
                    'hasMedia' => $hasMedia,
                    'hasLocation' => $hasLocation,
                    'hasDate' => $hasDate,
                    'grade' => $grade
                ]);

                $conditions = [
                    $config['id_column'] => $actualId
                ];

                if ($faunaId) {
                    $conditions[$config['fauna_column']] = $faunaId;
                }

                $assessmentData = [
                    'grade' => $grade,
                    'has_media' => $hasMedia,
                    'has_location' => $hasLocation,
                    'has_date' => $hasDate,
                    'agreement_count' => ($stats->agreement_count == 0) ? '' : $stats->agreement_count,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($source === 'fobi' && $faunaId) {
                    $assessmentData[$config['fauna_column']] = $faunaId;
                }

                Log::info('Attempting to update existing assessment', [
                    'conditions' => $conditions,
                    'data' => $assessmentData
                ]);

                // Coba update dulu yang existing
                $updated = DB::table($config['table'])
                    ->where($config['id_column'], $actualId)
                    ->update($assessmentData);

                if (!$updated) {
                    Log::info('No existing record updated, inserting new one');
                    // Jika tidak ada yang terupdate, baru insert baru
                    DB::table($config['table'])->insert(array_merge($conditions, $assessmentData));
                }

                $assessment = DB::table($config['table'])
                    ->where($conditions)
                    ->first();
            }

            // Format response
            $formattedAssessment = [
                'id' => $assessment->id ?? null,
                'grade' => $assessment->grade ?? 'casual',
                'has_media' => $assessment->has_media ?? false,
                'has_location' => $assessment->has_location ?? false,
                'has_date' => $assessment->has_date ?? false,
                'agreement_count' => $assessment->agreement_count ? (string)$assessment->agreement_count : ''
            ];

            Log::info('Returning formatted assessment', [
                'formattedAssessment' => $formattedAssessment
            ]);

            return response()->json([
                'success' => true,
                'data' => $formattedAssessment
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getQualityAssessment', [
                'id' => $id,
                'source' => $source ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil quality assessment'
            ], 500);
        }
    }

    private function checkHasLocation($id, $source)
    {
        $table = $this->getChecklistTable($source);
        return DB::table($table)
            ->where('id', $id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->exists();
    }

    private function checkHasDate($id, $source)
    {
        $table = $this->getChecklistTable($source);

        // Sesuaikan kolom berdasarkan source
        $dateColumn = match($source) {
            'fobi' => 'created_at',  // Gunakan created_at untuk FOBI
            'burungnesia', 'kupunesia' => 'tgl_pengamatan'
        };

        return DB::table($table)
            ->where('id', $id)
            ->whereNotNull($dateColumn)
            ->exists();
    }

    // Method untuk batch update quality assessments
    public function batchUpdateQualityAssessments(Request $request)
    {
        try {
            $request->validate([
                'observation_ids' => 'required|array',
                'observation_ids.*' => 'required|string'
            ]);

            $results = [];
            foreach ($request->observation_ids as $id) {
                $source = $this->determineSource($id);
                try {
                    $grade = $this->updateQualityAssessment($id, $source);
                    $results[$id] = [
                        'success' => true,
                        'grade' => $grade
                    ];
                } catch (\Exception $e) {
                    $results[$id] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error in batchUpdateQualityAssessments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui quality assessments'
            ], 500);
        }
    }

    // Method untuk menentukan sumber data berdasarkan ID
    private function determineSource($id)
    {
        if (str_starts_with($id, 'BN')) return 'burungnesia';
        if (str_starts_with($id, 'KP')) return 'kupunesia';
        return 'fobi';
    }

    // Method untuk mendapatkan statistik quality assessment
    public function getQualityStats(Request $request)
    {
        try {
            $source = $request->input('source', 'all');

            $stats = [];
            $sources = $source === 'all' ? ['fobi', 'burungnesia', 'kupunesia'] : [$source];

            foreach ($sources as $src) {
                $table = match($src) {
                    'fobi' => 'taxa_quality_assessments',
                    'burungnesia' => 'data_quality_assessments',
                    'kupunesia' => 'data_quality_assessments_kupnes'
                };

                $stats[$src] = DB::table($table)
                    ->select(
                        DB::raw('CASE WHEN COUNT(*) = 0 THEN NULL ELSE COUNT(*) END as total'),
                        DB::raw("CASE WHEN COUNT(CASE WHEN grade = 'research grade' THEN 1 END) = 0 THEN NULL ELSE COUNT(CASE WHEN grade = 'research grade' THEN 1 END) END as research_grade"),
                        DB::raw("CASE WHEN COUNT(CASE WHEN grade = 'needs ID' THEN 1 END) = 0 THEN NULL ELSE COUNT(CASE WHEN grade = 'needs ID' THEN 1 END) END as needs_id"),
                        DB::raw("CASE WHEN COUNT(CASE WHEN grade = 'low quality ID' THEN 1 END) = 0 THEN NULL ELSE COUNT(CASE WHEN grade = 'low quality ID' THEN 1 END) END as low_quality"),
                        DB::raw("CASE WHEN COUNT(CASE WHEN grade = 'casual' THEN 1 END) = 0 THEN NULL ELSE COUNT(CASE WHEN grade = 'casual' THEN 1 END) END as casual")
                    )
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getQualityStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik quality assessment'
            ], 500);
        }
    }

    public function assessQuality($id)
    {
        try {
            $source = $this->determineSource($id);
            $actualId = $this->getActualId($id, $source);

            // Tentukan tabel berdasarkan sumber
            $checklistTable = $this->getChecklistTable($source);
            $assessmentTable = $this->getAssessmentTable($source);

            // Ambil data checklist
            $checklist = DB::table($checklistTable)
                ->where('id', $actualId)
                ->first();

            if (!$checklist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checklist tidak ditemukan'
                ], 404);
            }

            // Ambil atau buat assessment
            $assessment = DB::table($assessmentTable)
                ->where('taxa_id', $actualId)
                ->first();

            if (!$assessment) {
                // Pastikan kita mendapatkan taxa_id yang benar
                $taxaId = null;
                if ($source === 'fobi') {
                    $taxaId = DB::table('fobi_checklist_taxas')
                        ->where('id', $actualId)
                        ->value(DB::raw('COALESCE(taxa_id, original_taxa_id)'));
                }

                // Buat assessment baru dengan taxa_id yang benar
                $assessment = [
                    'taxa_id' => $actualId,
                    'taxon_id' => $taxaId, // Gunakan taxa_id yang sudah diambil
                    'grade' => 'needs ID',
                    'has_date' => $this->checkHasDate($actualId, $source),
                    'has_location' => !empty($checklist->latitude) && !empty($checklist->longitude),
                    'has_media' => $this->checkHasMedia($actualId, $source),
                    'agreement_count' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Gunakan transaction untuk memastikan atomicity
                DB::beginTransaction();
                try {
                    // Gunakan updateOrInsert untuk mencegah duplikasi
                    DB::table($assessmentTable)->updateOrInsert(
                        ['taxa_id' => $actualId],
                        $assessment
                    );

                    // Ambil assessment yang baru dibuat
                    $assessment = DB::table($assessmentTable)
                        ->where('taxa_id', $actualId)
                        ->first();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $assessment
            ]);

        } catch (\Exception $e) {
            Log::error('Error in assessQuality: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil quality assessment'
            ], 500);
        }
    }

    public function updateQualityAssessment($id, $source)
    {
        try {
            $actualId = $this->getActualId($id, $source);

            // Get fauna_id first
            if ($source === 'burungnesia') {
                $fauna = DB::table('fobi_checklist_faunasv1')
                    ->where('checklist_id', $actualId)
                    ->first();
                $faunaId = $fauna ? $fauna->fauna_id : null;
            } elseif ($source === 'kupunesia') {
                $fauna = DB::table('fobi_checklist_faunasv2')
                    ->where('checklist_id', $actualId)
                    ->first();
                $faunaId = $fauna ? $fauna->fauna_id : null;
            } else {
                $fauna = DB::table('fobi_checklist_taxas')
                    ->where('id', $actualId)
                    ->first();
                $faunaId = $fauna ? $fauna->taxa_id : null;
            }

            // Get checklist details
            $hasMedia = $this->checkHasMedia($actualId, $source);
            $hasLocation = true; // Assuming all checklists have location
            $hasDate = true; // Assuming all checklists have date

            // Get identification stats
            $identificationStats = DB::table('taxa_identifications')
                ->where('checklist_id', $actualId)
                ->whereNull('is_withdrawn')
                ->selectRaw('COUNT(*) as total_identifications,
                            SUM(CASE WHEN agrees_with_id IS NOT NULL THEN 1 ELSE 0 END) as agreement_count')
                ->first();

            // Determine grade
            $grade = $this->determineGrade(
                $identificationStats->total_identifications ? (string)$identificationStats->total_identifications : '',
                $identificationStats->agreement_count ? (string)$identificationStats->agreement_count : '',
                $hasMedia,
                $hasLocation,
                $hasDate,
                $actualId
            );

            // Prepare assessment data
            $assessmentData = [
                'grade' => $grade,
                'has_media' => $hasMedia,
                'has_location' => $hasLocation,
                'has_date' => $hasDate,
                'fauna_id' => $faunaId,
                'updated_at' => now()
            ];

            // Update assessment
            $this->updateAssessment($id, $source, $assessmentData);

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating quality assessment: ' . $e->getMessage(), [
                'id' => $id,
                'source' => $source
            ]);
            throw $e;
        }
    }

    private function getFallbackFaunaId($actualId, $source)
    {
        try {
            if ($source === 'burungnesia') {
                $checklist = DB::table('fobi_checklist_faunasv1')
                    ->where('checklist_id', $actualId)
                    ->first();
                return $checklist->fauna_id ?? null;
            } elseif ($source === 'kupunesia') {
                $checklist = DB::table('fobi_checklist_faunasv2')
                    ->where('checklist_id', $actualId)
                    ->first();
                return $checklist->fauna_id ?? null;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting fallback fauna_id: ' . $e->getMessage());
            return null;
        }
    }

    private function updateChecklistTaxon($id, $source)
    {
        try {
            $actualId = $this->getActualId($id, $source);
            $user = auth()->user();

            // Base query untuk identifikasi dengan persetujuan terbanyak
            $query = DB::table('taxa_identifications as ti')
                ->select(
                    'ti.taxon_id',
                    'ti.burnes_fauna_id',
                    'ti.kupnes_fauna_id',
                    't.scientific_name',
                    't.taxon_key',
                    't.accepted_scientific_name',
                    't.taxon_rank',
                    't.taxonomic_status',
                    't.domain',
                    't.cname_domain',
                    't.superkingdom',
                    't.kingdom',
                    't.phylum',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    't.subspecies',
                    't.variety',
                    't.form',
                    't.subform',
                    't.cname_species',
                    't.iucn_red_list_category',
                    't.status_kepunahan',
                    DB::raw('COUNT(ti2.id) as agreement_count')
                )
                ->join('taxas as t', 'ti.taxon_id', '=', 't.id')
                ->leftJoin('taxa_identifications as ti2', 'ti.id', '=', 'ti2.agrees_with_id')
                ->where(function($query) use ($source, $actualId) {
                    if ($source === 'burungnesia') {
                        $query->where('ti.burnes_checklist_id', $actualId);
                    } elseif ($source === 'kupunesia') {
                        $query->where('ti.kupnes_checklist_id', $actualId);
                    } else {
                        $query->where('ti.checklist_id', $actualId);
                    }
                })
                ->where(function($query) {
                    $query->where('ti.is_withdrawn', false)
                          ->orWhereNull('ti.is_withdrawn');
                })
                ->groupBy(
                    'ti.taxon_id',
                    'ti.burnes_fauna_id',
                    'ti.kupnes_fauna_id',
                    't.scientific_name',
                    't.taxon_key',
                    't.accepted_scientific_name',
                    't.taxon_rank',
                    't.taxonomic_status',
                    't.domain',
                    't.cname_domain',
                    't.superkingdom',
                    't.kingdom',
                    't.phylum',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    't.subspecies',
                    't.variety',
                    't.form',
                    't.subform',
                    't.cname_species',
                    't.iucn_red_list_category',
                    't.status_kepunahan'
                )
                ->orderBy('agreement_count', 'desc')
                ->first();

            Log::info('Most agreed identification', [
                'identification' => $query,
                'source' => $source
            ]);

            if ($query) {
                if ($source === 'burungnesia') {
                    $currentFauna = DB::table('fobi_checklist_faunasv1')
                        ->where('checklist_id', $actualId)
                        ->first();

                    if ($currentFauna && $currentFauna->fauna_id != $query->burnes_fauna_id) {
                        // Ambil data taxa lengkap
                        $currentTaxa = DB::table('taxas')->find($currentFauna->fauna_id);
                        $newTaxa = DB::table('taxas')->find($query->taxon_id);

                        if ($currentTaxa && $newTaxa) {
                            $this->createTaxaIdentificationHistory(
                                $actualId,
                                $query->taxon_id,
                                $currentFauna->fauna_id,
                                $user->id,
                                $newTaxa,
                                $currentTaxa
                            );
                        }

                        DB::table('fobi_checklist_faunasv1')
                            ->where('checklist_id', $actualId)
                            ->update([
                                'fauna_id' => $query->burnes_fauna_id,
                                'updated_at' => now()
                            ]);

                        Log::info('Updated Burungnesia fauna', [
                            'checklist_id' => $actualId,
                            'fauna_id' => $query->burnes_fauna_id
                        ]);
                    }

                } elseif ($source === 'kupunesia') {
                    $currentFauna = DB::table('fobi_checklist_faunasv2')
                        ->where('checklist_id', $actualId)
                        ->first();

                    if ($currentFauna && $currentFauna->fauna_id != $query->kupnes_fauna_id) {
                        // Ambil data taxa lengkap
                        $currentTaxa = DB::table('taxas')->find($currentFauna->fauna_id);
                        $newTaxa = DB::table('taxas')->find($query->taxon_id);

                        if ($currentTaxa && $newTaxa) {
                            $this->createTaxaIdentificationHistory(
                                $actualId,
                                $query->taxon_id,
                                $currentFauna->fauna_id,
                                $user->id,
                                $newTaxa,
                                $currentTaxa
                            );
                        }

                        DB::table('fobi_checklist_faunasv2')
                            ->where('checklist_id', $actualId)
                            ->update([
                                'fauna_id' => $query->kupnes_fauna_id,
                                'updated_at' => now()
                            ]);

                        Log::info('Updated Kupunesia fauna', [
                            'checklist_id' => $actualId,
                            'fauna_id' => $query->kupnes_fauna_id
                        ]);
                    }

                } else {
                    $currentChecklist = DB::table('fobi_checklist_taxas')
                        ->where('id', $actualId)
                        ->first();

                    if ($currentChecklist && $currentChecklist->taxa_id != $query->taxon_id) {
                        // Ambil data taxa lengkap
                        $currentTaxa = DB::table('taxas')->find($currentChecklist->taxa_id);
                        $newTaxa = DB::table('taxas')->find($query->taxon_id);

                        if ($currentTaxa && $newTaxa) {
                            $this->createTaxaIdentificationHistory(
                                $actualId,
                                $query->taxon_id,
                                $currentChecklist->taxa_id,
                                $user->id,
                                $newTaxa,
                                $currentTaxa
                            );
                        }

                        DB::table('fobi_checklist_taxas')
                            ->where('id', $actualId)
                            ->update([
                                'original_taxa_id' => $currentChecklist->taxa_id,
                                'taxa_id' => $query->taxon_id,
                                'scientific_name' => $newTaxa->scientific_name,
                                'class' => $newTaxa->class,
                                'order' => $newTaxa->order,
                                'family' => $newTaxa->family,
                                'genus' => $newTaxa->genus,
                                'species' => $newTaxa->species,
                                'updated_at' => now()
                            ]);

                        Log::info('Updated FOBI taxa', [
                            'checklist_id' => $actualId,
                            'taxa_id' => $query->taxon_id,
                            'scientific_name' => $newTaxa->scientific_name
                        ]);
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error updating checklist taxon', [
                'checklist_id' => $actualId,
                'source' => $source,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Method baru untuk membuat history
    private function createTaxaIdentificationHistory($checklistId, $taxaId, $previousTaxaId, $userId, $newTaxa, $currentTaxa)
    {
        try {
            TaxaIdentificationHistory::create([
                'checklist_id' => $checklistId,
                'taxa_id' => $taxaId,
                'previous_taxa_id' => $previousTaxaId,
                'user_id' => $userId,
                'action_type' => 'change',
                'scientific_name' => $newTaxa->scientific_name,
                'taxon_key' => $newTaxa->taxon_key,
                'accepted_scientific_name' => $newTaxa->accepted_scientific_name,
                'taxon_rank' => $newTaxa->taxon_rank,
                'taxonomic_status' => $newTaxa->taxonomic_status,

                // Current taxonomy data
                'current_taxonomy' => [
                    'domain' => $newTaxa->domain,
                    'cname_domain' => $newTaxa->cname_domain,
                    'superkingdom' => $newTaxa->superkingdom,
                    'kingdom' => $newTaxa->kingdom,
                    'phylum' => $newTaxa->phylum,
                    'class' => $newTaxa->class,
                    'order' => $newTaxa->order,
                    'family' => $newTaxa->family,
                    'genus' => $newTaxa->genus,
                    'species' => $newTaxa->species,
                    'subspecies' => $newTaxa->subspecies,
                    'variety' => $newTaxa->variety,
                    'form' => $newTaxa->form,
                    'subform' => $newTaxa->subform,
                    'cname_species' => $newTaxa->cname_species,
                    'iucn_red_list_category' => $newTaxa->iucn_red_list_category,
                    'status_kepunahan' => $newTaxa->status_kepunahan
                ],

                // Previous taxonomy data
                'previous_taxonomy' => [
                    'domain' => $currentTaxa->domain,
                    'cname_domain' => $currentTaxa->cname_domain,
                    'superkingdom' => $currentTaxa->superkingdom,
                    'kingdom' => $currentTaxa->kingdom,
                    'phylum' => $currentTaxa->phylum,
                    'class' => $currentTaxa->class,
                    'order' => $currentTaxa->order,
                    'family' => $currentTaxa->family,
                    'genus' => $currentTaxa->genus,
                    'species' => $currentTaxa->species,
                    'subspecies' => $currentTaxa->subspecies,
                    'variety' => $currentTaxa->variety,
                    'form' => $currentTaxa->form,
                    'subform' => $currentTaxa->subform,
                    'cname_species' => $currentTaxa->cname_species,
                    'iucn_red_list_category' => $currentTaxa->iucn_red_list_category,
                    'status_kepunahan' => $currentTaxa->status_kepunahan
                ],
                'reason' => 'Auto-updated based on consensus'
            ]);

            Log::info('Created taxa identification history', [
                'checklist_id' => $checklistId,
                'taxa_id' => $taxaId,
                'previous_taxa_id' => $previousTaxaId
            ]);

            // Tambahkan tracking untuk taxa yang sering tertukar
            $this->updateSimilarTaxa($previousTaxaId, $taxaId);

        } catch (\Exception $e) {
            Log::error('Error creating taxa identification history', [
                'checklist_id' => $checklistId,
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function updateSimilarTaxa($previousTaxaId, $newTaxaId)
    {
        try {
            if (!$previousTaxaId || !$newTaxaId || $previousTaxaId == $newTaxaId) {
                return;
            }

            // Ambil data taxa untuk menentukan similarity type
            $previousTaxa = DB::table('taxas')->find($previousTaxaId);
            $newTaxa = DB::table('taxas')->find($newTaxaId);

            if (!$previousTaxa || !$newTaxa) {
                return;
            }

            // Tentukan tipe kemiripan
            $similarityType = $this->determineSimilarityType($previousTaxa, $newTaxa);

            // Update atau buat record baru
            TaxaSimilarIdentification::updateOrCreate(
                [
                    'taxa_id' => min($previousTaxaId, $newTaxaId),
                    'similar_taxa_id' => max($previousTaxaId, $newTaxaId)
                ],
                [
                    'similarity_type' => $similarityType,
                    'confusion_count' => DB::raw('confusion_count + 1'),
                    'notes' => $this->generateSimilarityNotes($previousTaxa, $newTaxa)
                ]
            );

            Log::info('Updated similar taxa record', [
                'previous_taxa' => $previousTaxaId,
                'new_taxa' => $newTaxaId,
                'similarity_type' => $similarityType
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating similar taxa: ' . $e->getMessage(), [
                'previous_taxa' => $previousTaxaId,
                'new_taxa' => $newTaxaId
            ]);
        }
    }

    private function determineSimilarityType($previousTaxa, $newTaxa)
    {
        if ($previousTaxa->genus === $newTaxa->genus) {
            if ($previousTaxa->species === $newTaxa->species) {
                if ($previousTaxa->subspecies === $newTaxa->subspecies) {
                    return 'variety';
                }
                return 'subspecies';
            }
            return 'species';
        }
        return 'genus';
    }

    private function generateSimilarityNotes($previousTaxa, $newTaxa)
    {
        $differences = [];

        // Bandingkan karakteristik utama
        if ($previousTaxa->genus !== $newTaxa->genus) {
            $differences[] = "Genus berbeda: {$previousTaxa->genus} vs {$newTaxa->genus}";
        }
        if ($previousTaxa->species !== $newTaxa->species) {
            $differences[] = "Species berbeda: {$previousTaxa->species} vs {$newTaxa->species}";
        }
        if ($previousTaxa->subspecies !== $newTaxa->subspecies) {
            $differences[] = "Subspecies berbeda: {$previousTaxa->subspecies} vs {$newTaxa->subspecies}";
        }

        return implode("; ", $differences);
    }

    // Method baru untuk mendapatkan taxa yang sering tertukar
    public function getSimilarTaxa($taxaId)
    {
        try {
            $similarTaxa = TaxaSimilarIdentification::where(function($query) use ($taxaId) {
                    $query->where('taxa_id', $taxaId)
                          ->orWhere('similar_taxa_id', $taxaId);
                })
                ->orderBy('confusion_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) use ($taxaId) {
                    $similarId = $item->taxa_id == $taxaId ?
                        $item->similar_taxa_id : $item->taxa_id;

                    $similarTaxa = DB::table('taxas')->find($similarId);

                    return [
                        'id' => $similarId,
                        'scientific_name' => $similarTaxa->scientific_name,
                        'confusion_count' => $item->confusion_count,
                        'similarity_type' => $item->similarity_type,
                        'notes' => $item->notes
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $similarTaxa
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting similar taxa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data taxa yang mirip'
            ], 500);
        }
    }

    public function updateImprovementStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'can_be_improved' => 'required|boolean'
            ]);

            $source = request()->query('source', $this->determineSource($id));
            $actualId = $this->getActualId($id, $source);
            $config = $this->getAssessmentConfig($source);

            // Hitung jumlah identifikasi dan persetujuan
            $identificationStats = DB::table('taxa_identifications')
                ->where(function($query) use ($source, $actualId) {
                    if ($source === 'burungnesia') {
                        $query->where('burnes_checklist_id', $actualId);
                    } elseif ($source === 'kupunesia') {
                        $query->where('kupnes_checklist_id', $actualId);
                    } else {
                        $query->where('checklist_id', $actualId);
                    }
                })
                ->selectRaw('
                    COUNT(*) as total_identifications,
                    COUNT(CASE WHEN agrees_with_id IS NOT NULL THEN 1 END) as agreement_count
                ')
                ->first();

            // Cek keberadaan media dan lokasi berdasarkan sumber
            $hasMedia = $this->checkHasMedia($actualId, $source);
            $hasLocation = $this->checkHasLocation($actualId, $source);
            $hasDate = $this->checkHasDate($actualId, $source);

            // Ambil atau buat assessment sesuai sumber
            $assessment = DB::table($config['table'])->where($config['id_column'], $actualId)->first();

            if (!$assessment) {
                // Buat assessment baru
                $assessmentData = [
                    $config['id_column'] => $actualId,
                    'grade' => 'needs ID',
                    'has_media' => $hasMedia,
                    'has_location' => $hasLocation,
                    'has_date' => $hasDate,
                    'is_wild' => true,
                    'location_accurate' => true,
                    'recent_evidence' => true,
                    'related_evidence' => true,
                    'can_be_improved' => $request->can_be_improved,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table($config['table'])->insert($assessmentData);
                $assessment = (object)$assessmentData;
            } else {
                // Update assessment yang ada
                $updateData = [
                    'can_be_improved' => $request->can_be_improved,
                    'updated_at' => now()
                ];

                // Evaluasi grade berdasarkan can_be_improved
                if ($request->can_be_improved) {
                    if ($this->determineGrade(
                        $identificationStats->total_identifications,
                        $identificationStats->agreement_count,
                        $hasMedia,
                        $hasLocation,
                        $hasDate,
                        $actualId
                    ) == 'needs ID') {
                        $updateData['grade'] = 'needs ID';
                    }
                } else {
                    // Langsung set research grade ketika can_be_improved false
                    $updateData['grade'] = 'research grade';
                }

                DB::table($config['table'])
                    ->where($config['id_column'], $actualId)
                    ->update($updateData);

                // Refresh assessment data
                $assessment = DB::table($config['table'])
                    ->where($config['id_column'], $actualId)
                    ->first();
            }

            // Update checklist taxon jika grade berubah
            $this->updateChecklistTaxon($id, $source);

            return response()->json([
                'success' => true,
                'data' => $assessment,
                'message' => 'Status berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating improvement status: ' . $e->getMessage(), [
                'id' => $id,
                'source' => $source ?? 'unknown',
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status'
            ], 500);
        }
    }

    private function updateAssessment($id, $source, $assessmentData)
    {
        try {
            $config = $this->getAssessmentConfig($source);
            $actualId = $this->getActualId($id, $source);

            // Get fauna/taxon id based on source
            if ($source === 'burungnesia') {
                $fauna = DB::table('fobi_checklist_faunasv1')
                    ->where('checklist_id', $actualId)
                    ->first();
                $faunaId = $fauna ? $fauna->fauna_id : null;

                if (!$faunaId) {
                    $lastIdentification = DB::table('taxa_identifications')
                        ->where('burnes_checklist_id', $actualId)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $faunaId = $lastIdentification ? $lastIdentification->burnes_fauna_id : null;
                }

                $assessmentData['fauna_id'] = $faunaId;

            } elseif ($source === 'kupunesia') {
                $fauna = DB::table('fobi_checklist_faunasv2')
                    ->where('checklist_id', $actualId)
                    ->first();
                $faunaId = $fauna ? $fauna->fauna_id : null;

                if (!$faunaId) {
                    $lastIdentification = DB::table('taxa_identifications')
                        ->where('kupnes_checklist_id', $actualId)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $faunaId = $lastIdentification ? $lastIdentification->kupnes_fauna_id : null;
                }

                $assessmentData['fauna_id'] = $faunaId;
            } else {
                // Untuk FOBI, gunakan taxon_id bukan fauna_id
                $taxa = DB::table('fobi_checklist_taxas')
                    ->where('id', $actualId)
                    ->first();
                $taxonId = $taxa ? $taxa->taxa_id : null;

                if (!$taxonId) {
                    $lastIdentification = DB::table('taxa_identifications')
                        ->where('checklist_id', $actualId)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $taxonId = $lastIdentification ? $lastIdentification->taxon_id : null;
                }

                $assessmentData['taxon_id'] = $taxonId;
                // Hapus fauna_id jika ada untuk menghindari error
                unset($assessmentData['fauna_id']);
            }

            // Check if assessment exists
            $existingAssessment = DB::table($config['table'])
                ->where($config['id_column'], $actualId)
                ->first();

            if ($existingAssessment) {
                // Update existing assessment
                DB::table($config['table'])
                    ->where($config['id_column'], $actualId)
                    ->update($assessmentData);
            } else {
                // Create new assessment
                $assessmentData[$config['id_column']] = $actualId;
                $assessmentData['created_at'] = now();

                // Pastikan fauna_id/taxon_id ada sebelum insert
                if ($source === 'fobi') {
                    if (!isset($assessmentData['taxon_id']) || is_null($assessmentData['taxon_id'])) {
                        Log::warning('No taxon_id found for assessment', [
                            'source' => $source,
                            'checklist_id' => $actualId
                        ]);
                        $assessmentData['taxon_id'] = null;
                    }
                } else {
                    if (!isset($assessmentData['fauna_id']) || is_null($assessmentData['fauna_id'])) {
                        Log::warning('No fauna_id found for assessment', [
                            'source' => $source,
                            'checklist_id' => $actualId
                        ]);
                        $assessmentData['fauna_id'] = null;
                    }
                }

                DB::table($config['table'])->insert($assessmentData);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error in updateAssessment: ' . $e->getMessage(), [
                'id' => $id,
                'source' => $source,
                'data' => $assessmentData,
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
