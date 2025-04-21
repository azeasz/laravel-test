<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\QualityAssessmentTrait;
use App\Models\DataQualityAssessment;
use App\Models\DataQualityAssessmentKupnes;
use App\Models\CommunityIdentification;
use App\Models\LocationVerification;
use App\Models\WildStatusVote;
use App\Models\EvidenceVerification;

class QualityAssessmentController extends Controller
{
    use QualityAssessmentTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = match ($request->type) {
                'kupunesia' => DataQualityAssessmentKupnes::query(),
                default => DataQualityAssessment::query(),
            };

            $assessments = $query->with(['observation'])
                ->when($request->grade, function($q) use ($request) {
                    return $q->where('grade', $request->grade);
                })
                ->when($request->needs_id, function($q) {
                    return $q->where('needs_id', true);
                })
                ->paginate($request->per_page ?? 15);

            return response()->json([
                'success' => true,
                'data' => $assessments
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching quality assessments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data penilaian kualitas.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'observation_id' => 'required|integer',
                'observation_type' => 'required|in:burungnesia,kupunesia,general',
                'fauna_id' => 'required|integer'
            ]);

            DB::transaction(function() use ($request) {
                // Ambil data observasi
                $observation = match ($request->observation_type) {
                    'kupunesia' => DB::table('fobi_checklists_kupnes')->find($request->observation_id),
                    default => DB::table('fobi_checklists')->find($request->observation_id),
                };

                if (!$observation) {
                    throw new \Exception('Observasi tidak ditemukan.');
                }

                // Assess quality menggunakan trait
                $quality = $this->assessQuality($observation);

                // Simpan assessment
                $model = match ($request->observation_type) {
                    'kupunesia' => DataQualityAssessmentKupnes::class,
                    default => DataQualityAssessment::class,
                };

                $model::create([
                    'observation_id' => $request->observation_id,
                    'fauna_id' => $request->fauna_id,
                    'grade' => $quality['grade'],
                    'has_date' => $quality['has_date'],
                    'has_location' => $quality['has_location'],
                    'has_media' => $quality['has_media'],
                    'is_wild' => $quality['is_wild'],
                    'location_accurate' => $quality['location_accurate'],
                    'recent_evidence' => $quality['recent_evidence'],
                    'related_evidence' => $quality['related_evidence'],
                    'needs_id' => $quality['needs_id'],
                    'community_id_level' => $quality['community_id_level']
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kualitas berhasil disimpan.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing quality assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan penilaian kualitas.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        try {
            $model = match ($request->type) {
                'kupunesia' => DataQualityAssessmentKupnes::class,
                default => DataQualityAssessment::class,
            };

            $assessment = $model::with([
                'observation',
                'communityIdentifications',
                'locationVerifications',
                'wildStatusVotes',
                'evidenceVerifications'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $assessment
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching quality assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data penilaian kualitas.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'grade' => 'sometimes|in:casual,needs ID,research grade',
                'is_wild' => 'sometimes|boolean',
                'location_accurate' => 'sometimes|boolean',
                'recent_evidence' => 'sometimes|boolean',
                'related_evidence' => 'sometimes|boolean',
                'needs_id' => 'sometimes|boolean',
                'community_id_level' => 'sometimes|string'
            ]);

            $model = match ($request->type) {
                'kupunesia' => DataQualityAssessmentKupnes::class,
                default => DataQualityAssessment::class,
            };

            $assessment = $model::findOrFail($id);

            DB::transaction(function() use ($request, $assessment) {
                // Update assessment
                $assessment->update($request->only([
                    'grade',
                    'is_wild',
                    'location_accurate',
                    'recent_evidence',
                    'related_evidence',
                    'needs_id',
                    'community_id_level'
                ]));

                // Re-assess quality jika diperlukan
                if ($request->reassess) {
                    $observation = match ($assessment->observation_type) {
                        'kupunesia' => DB::table('fobi_checklists_kupnes')->find($assessment->observation_id),
                        default => DB::table('fobi_checklists')->find($assessment->observation_id),
                    };

                    $quality = $this->assessQuality($observation);
                    $assessment->update($quality);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kualitas berhasil diperbarui.',
                'data' => $assessment
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating quality assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui penilaian kualitas.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $model = match ($request->type) {
                'kupunesia' => DataQualityAssessmentKupnes::class,
                default => DataQualityAssessment::class,
            };

            $assessment = $model::findOrFail($id);
            $assessment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kualitas berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting quality assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus penilaian kualitas.'
            ], 500);
        }
    }

    /**
     * Add community identification
     */
    public function addIdentification(Request $request)
    {
        try {
            $request->validate([
                'observation_id' => 'required|integer',
                'observation_type' => 'required|in:burungnesia,kupunesia,general',
                'taxon_id' => 'required|integer',
                'identification_level' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            $identification = CommunityIdentification::create([
                'observation_id' => $request->observation_id,
                'observation_type' => $request->observation_type,
                'user_id' => auth()->id(),
                'taxon_id' => $request->taxon_id,
                'identification_level' => $request->identification_level,
                'notes' => $request->notes
            ]);

            // Reassess quality setelah identifikasi baru
            $this->reassessQuality($request->observation_id, $request->observation_type);

            return response()->json([
                'success' => true,
                'message' => 'Identifikasi berhasil ditambahkan.',
                'data' => $identification
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding identification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan identifikasi.'
            ], 500);
        }
    }

    /**
     * Add location verification
     */
    public function verifyLocation(Request $request)
    {
        try {
            $request->validate([
                'observation_id' => 'required|integer',
                'observation_type' => 'required|in:burungnesia,kupunesia,general',
                'is_accurate' => 'required|boolean',
                'reason' => 'nullable|string'
            ]);

            $verification = LocationVerification::create([
                'observation_id' => $request->observation_id,
                'observation_type' => $request->observation_type,
                'user_id' => auth()->id(),
                'is_accurate' => $request->is_accurate,
                'reason' => $request->reason
            ]);

            // Reassess quality setelah verifikasi lokasi
            $this->reassessQuality($request->observation_id, $request->observation_type);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi lokasi berhasil ditambahkan.',
                'data' => $verification
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding location verification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan verifikasi lokasi.'
            ], 500);
        }
    }

    /**
     * Add wild status vote
     */
    public function voteWildStatus(Request $request)
    {
        try {
            $request->validate([
                'observation_id' => 'required|integer',
                'observation_type' => 'required|in:burungnesia,kupunesia,general',
                'is_wild' => 'required|boolean',
                'reason' => 'nullable|string'
            ]);

            $vote = WildStatusVote::create([
                'observation_id' => $request->observation_id,
                'observation_type' => $request->observation_type,
                'user_id' => auth()->id(),
                'is_wild' => $request->is_wild,
                'reason' => $request->reason
            ]);

            // Reassess quality setelah voting
            $this->reassessQuality($request->observation_id, $request->observation_type);

            return response()->json([
                'success' => true,
                'message' => 'Vote status wild berhasil ditambahkan.',
                'data' => $vote
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding wild status vote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan vote status wild.'
            ], 500);
        }
    }

    /**
     * Add evidence verification
     */
    public function verifyEvidence(Request $request)
    {
        try {
            $request->validate([
                'observation_id' => 'required|integer',
                'observation_type' => 'required|in:burungnesia,kupunesia,general',
                'is_valid_evidence' => 'required|boolean',
                'is_recent' => 'required|boolean',
                'is_related' => 'required|boolean',
                'notes' => 'nullable|string'
            ]);

            $verification = EvidenceVerification::create([
                'observation_id' => $request->observation_id,
                'observation_type' => $request->observation_type,
                'user_id' => auth()->id(),
                'is_valid_evidence' => $request->is_valid_evidence,
                'is_recent' => $request->is_recent,
                'is_related' => $request->is_related,
                'notes' => $request->notes
            ]);

            // Reassess quality setelah verifikasi bukti
            $this->reassessQuality($request->observation_id, $request->observation_type);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi bukti berhasil ditambahkan.',
                'data' => $verification
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding evidence verification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan verifikasi bukti.'
            ], 500);
        }
    }

    /**
     * Reassess quality for an observation
     */
    private function reassessQuality($observationId, $observationType)
    {
        $observation = match ($observationType) {
            'kupunesia' => DB::table('fobi_checklists_kupnes')->find($observationId),
            default => DB::table('fobi_checklists')->find($observationId),
        };

        if (!$observation) {
            throw new \Exception('Observasi tidak ditemukan.');
        }

        $quality = $this->assessQuality($observation);

        $model = match ($observationType) {
            'kupunesia' => DataQualityAssessmentKupnes::class,
            default => DataQualityAssessment::class,
        };

        $assessment = $model::where('observation_id', $observationId)->first();
        if ($assessment) {
            $assessment->update($quality);
        }
    }
}
