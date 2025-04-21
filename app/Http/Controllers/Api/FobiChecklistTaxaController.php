<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FobiChecklistTaxa;
use App\Models\TaxaQualityAssessment;

class FobiChecklistTaxaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'scientific_name' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'media_id' => 'nullable|exists:fobi_checklist_media,id',
            'user_id' => 'required|exists:fobi_users,id',
            'observation_details' => 'nullable|string',
            'taxon_key' => 'nullable|integer',
        ]);

        Log::info('Data yang diterima:', $validatedData);

        try {
            // Cari taxa berdasarkan scientific_name atau taxon_key
            $taxa = DB::table('taxa')
                ->where('scientific_name', $validatedData['scientific_name'])
                ->orWhere('taxon_key', $validatedData['taxon_key'])
                ->first();

            if (!$taxa) {
                Log::warning('Taxa tidak ditemukan untuk:', $validatedData);
                return response()->json([
                    'success' => false,
                    'message' => 'Taxa tidak ditemukan.'
                ], 404);
            }

            // Simpan data ke fobi_checklist_taxas
            $checklistTaxa = FobiChecklistTaxa::create([
                'taxa_id' => $taxa->id,
                'user_id' => $validatedData['user_id'],
                'media_id' => $validatedData['media_id'],
                'scientific_name' => $validatedData['scientific_name'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'observation_details' => $validatedData['observation_details'],
            ]);

            Log::info('Data berhasil disimpan ke fobi_checklist_taxas:', $checklistTaxa->toArray());

            // Buat penilaian kualitas
            TaxaQualityAssessment::create([
                'checklist_taxa_id' => $checklistTaxa->id,
                'grade' => 'casual',
                'has_date' => false,
                'has_location' => false,
                'has_media' => false,
                'is_wild' => true,
                'location_accurate' => true,
                'recent_evidence' => true,
                'related_evidence' => true,
                'needs_id' => false,
                'community_id_level' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Observasi berhasil disimpan dan dinilai.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing observation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan observasi.'
            ], 500);
        }
    }
}
