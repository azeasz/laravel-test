<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\TaxaQualityAssessmentTrait;
use App\Models\TaxaQualityAssessment;

class TaxaQualityAssessmentController extends Controller
{
    use TaxaQualityAssessmentTrait;

    public function index(Request $request)
    {
        try {
            $assessments = TaxaQualityAssessment::with(['taxas'])
                ->when($request->grade, function($q) use ($request) {
                    return $q->where('grade', $request->grade);
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'checklist_taxa_id' => 'required|integer'
            ]);

            DB::transaction(function() use ($request) {
                $taxa = DB::table('taxa')->find($request->taxa_id);

                if (!$taxa) {
                    throw new \Exception('Taxa tidak ditemukan.');
                }

                $quality = $this->assessTaxaQuality($taxa);

                TaxaQualityAssessment::create([
                    'checklist_taxa_id' => $request->taxa_id,
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
}
