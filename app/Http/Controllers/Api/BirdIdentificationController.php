<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class BirdIdentificationController extends Controller
{
    public function addIdentification(Request $request, $id)
    {
        try {
            $request->validate([
                'taxon_id' => 'required|exists:taxas,id',
                'comment' => 'nullable|string|max:500',
                'photo' => 'nullable|image|max:5120',
                'identification_level' => 'required|string'
            ]);

            DB::beginTransaction();
            $user = JWTAuth::user();
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('bird-identification-photos', 'public');
            }

            $identificationId = DB::table('bird_identifications')->insertGetId([
                'checklist_id' => $id,
                'user_id' => $user->id,
                'taxon_id' => $request->taxon_id,
                'identification_level' => $request->identification_level,
                'comment' => $request->comment,
                'photo_path' => $photoPath,
                'is_first' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Identifikasi berhasil ditambahkan',
                'data' => [
                    'id' => $identificationId,
                    'photo_url' => $photoPath ? asset('storage/' . $photoPath) : null
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding bird identification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan identifikasi'
            ], 500);
        }
    }

    public function withdrawIdentification($checklistId, $identificationId)
    {
        try {
            DB::beginTransaction();

            DB::table('bird_identifications')
                ->where('id', $identificationId)
                ->update(['is_withdrawn' => true]);

            DB::table('bird_identifications')
                ->where('agrees_with_id', $identificationId)
                ->delete();

            DB::table('bird_quality_assessments')
                ->where('checklist_id', $checklistId)
                ->update([
                    'community_id_level' => null,
                    'grade' => 'needs ID'
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Identifikasi berhasil ditarik'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error withdrawing bird identification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menarik identifikasi'
            ], 500);
        }
    }

    public function agreeWithIdentification($checklistId, $identificationId)
    {
        try {
            DB::beginTransaction();
            $user = JWTAuth::user();

            $existingAgreement = DB::table('bird_identifications')
                ->where('checklist_id', $checklistId)
                ->where('user_id', $user->id)
                ->where('agrees_with_id', $identificationId)
                ->exists();

            if ($existingAgreement) {
                throw new \Exception('Anda sudah menyetujui identifikasi ini');
            }

            $agreedIdentification = DB::table('bird_identifications as bi')
                ->join('burung as b', 'b.id', '=', 'bi.taxon_id')
                ->where('bi.id', $identificationId)
                ->first();

            DB::table('bird_identifications')->insert([
                'checklist_id' => $checklistId,
                'user_id' => $user->id,
                'agrees_with_id' => $identificationId,
                'taxon_id' => $agreedIdentification->taxon_id,
                'identification_level' => $agreedIdentification->identification_level,
                'is_agreed' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $agreementCount = DB::table('bird_identifications')
                ->where('agrees_with_id', $identificationId)
                ->where('is_agreed', true)
                ->count();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Persetujuan berhasil ditambahkan',
                'data' => [
                    'agreement_count' => $agreementCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error agreeing with bird identification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function disagreeWithIdentification(Request $request, $checklistId, $identificationId)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:500',
                'photo' => 'nullable|image|max:2048'
            ]);

            DB::beginTransaction();
            $user = JWTAuth::user();
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('bird-identification-photos', 'public');
            }

            DB::table('bird_identifications')->insert([
                'checklist_id' => $checklistId,
                'user_id' => $user->id,
                'disagrees_with_id' => $identificationId,
                'comment' => $request->comment,
                'photo_path' => $photoPath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ketidaksetujuan berhasil direkam'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error disagreeing with bird identification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat merekam ketidaksetujuan'
            ], 500);
        }
    }

    public function searchBirds(Request $request)
    {
        try {
            $query = $request->get('q');

            if (strlen($query) < 3) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $birds = DB::table('taxas')
                ->where('scientific_name', 'LIKE', "%{$query}%")
                ->orWhere('cname_species', 'LIKE', "%{$query}%")
                ->select('id', 'scientific_name', 'cname_species', 'family', 'genus', 'species')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $birds
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching birds: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data burung'
            ], 500);
        }
    }
}
