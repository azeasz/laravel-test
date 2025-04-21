<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class BirdObservationController extends Controller
{
    public function show($id)
    {
        try {
            $userId = auth()->id();

            // Ambil data checklist dasar
            $baseChecklist = DB::select("
                SELECT
                    fc.*,
                    fu.uname as observer_name,
                    dqa.grade as quality_grade,
                    dqa.has_media,
                    dqa.has_date,
                    dqa.has_location,
                    dqa.is_wild,
                    dqa.location_accurate,
                    dqa.needs_id,
                    dqa.community_id_level,
                    COALESCE(t.scientific_name, f.nameLat) as scientific_name,
                    COALESCE(t.cname_species, f.nameId) as common_name,
                    t.kingdom,
                    t.phylum,
                    t.class,
                    t.order,
                    f.family,
                    t.genus,
                    t.species
                FROM fobi_checklists fc
                JOIN fobi_users fu ON fc.fobi_user_id = fu.id
                LEFT JOIN data_quality_assessments dqa ON fc.id = dqa.observation_id
                LEFT JOIN fobi_checklist_faunasv1 cf ON fc.id = cf.checklist_id
                LEFT JOIN " . DB::connection('second')->getDatabaseName() . ".faunas f ON cf.fauna_id = f.id
                LEFT JOIN taxas t ON cf.fauna_id = t.burnes_fauna_id
                WHERE fc.id = ?
            ", [$id]);

            if (empty($baseChecklist)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checklist tidak ditemukan'
                ], 404);
            }

            $baseChecklist = $baseChecklist[0];

            // Ambil media (foto)
            $medias = DB::select("
                SELECT
                    id,
                    images as url,
                    'image' as type
                FROM fobi_checklist_fauna_imgs
                WHERE checklist_id = ?

                UNION ALL

                SELECT
                    id,
                    sounds as url,
                    'audio' as type
                FROM fobi_checklist_sounds
                WHERE checklist_id = ?

                UNION ALL

                SELECT
                    id,
                    spectrogram as url,
                    'spectrogram' as type
                FROM fobi_checklist_sounds
                WHERE checklist_id = ?
            ", [$id, $id, $id]);

            // Format media
            $formattedMedias = array_map(function($media) {
                return [
                    'id' => $media->id,
                    'type' => $media->type,
                    'url' => $media->url
                ];
            }, $medias);

            // Ambil identifikasi
            $identifications = DB::select("
                SELECT
                    bi.*,
                    u.uname as identifier_name,
                    u.created_at as identifier_joined_date,
                    COALESCE(t.scientific_name, f.nameLat) as scientific_name,
                    COALESCE(t.cname_species, f.nameId) as common_name,
                    f.family as taxon_rank,
                    (SELECT COUNT(*) FROM burungnesia_identifications WHERE user_id = u.id) as identifier_identification_count,
                    (SELECT COUNT(*) FROM burungnesia_identifications WHERE agrees_with_id = bi.id) as agreement_count,
                    (SELECT COUNT(*) > 0 FROM burungnesia_identifications WHERE agrees_with_id = bi.id AND user_id = ?) as user_agreed
                FROM burungnesia_identifications bi
                JOIN fobi_users u ON bi.user_id = u.id
                LEFT JOIN " . DB::connection('second')->getDatabaseName() . ".faunas f ON bi.taxon_id = f.id
                LEFT JOIN taxas t ON bi.taxon_id = t.burnes_fauna_id
                WHERE bi.observation_id = ? AND bi.observation_type = 'burungnesia'
            ", [$userId, $id]);

            // Format identifikasi
            $formattedIdentifications = array_map(function($identification) {
                return [
                    'id' => $identification->id,
                    'user_id' => $identification->user_id,
                    'identifier_name' => $identification->identifier_name,
                    'created_at' => $identification->created_at,
                    'taxon' => [
                        'scientific_name' => $identification->scientific_name,
                        'common_name' => $identification->common_name,
                        'taxon_rank' => $identification->taxon_rank
                    ],
                    'comment' => $identification->comment,
                    'agreement_count' => $identification->agreement_count,
                    'user_agreed' => (bool)$identification->user_agreed,
                    'identifier_identification_count' => $identification->identifier_identification_count
                ];
            }, $identifications);

            return response()->json([
                'success' => true,
                'data' => [
                    'checklist' => $baseChecklist,
                    'medias' => $formattedMedias,
                    'identifications' => $formattedIdentifications
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in BirdObservationController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail checklist'
            ], 500);
        }
    }

    public function addIdentification(Request $request, $checklistId)
    {
        try {
            $request->validate([
                'taxon_id' => 'required|exists:taxas,burnes_fauna_id',
                'comment' => 'nullable|string|max:1000',
                'photo' => 'nullable|image|max:5120' // max 5MB
            ]);

            DB::beginTransaction();

            // Simpan identifikasi
            $identificationId = DB::table('burungnesia_identifications')->insertGetId([
                'observation_id' => $checklistId,
                'observation_type' => 'burungnesia',
                'taxon_id' => $request->taxon_id,
                'user_id' => auth()->id(),
                'comment' => $request->comment,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Jika ada foto, proses dan simpan
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('bird-identifications', 'public');

                DB::table('burungnesia_identifications')
                    ->where('id', $identificationId)
                    ->update(['photo' => $photoPath]);
            }

            DB::commit();

            // Ambil data identifikasi yang baru dibuat
            $identification = DB::select("
                SELECT
                    bi.*,
                    u.uname as identifier_name,
                    t.scientific_name,
                    t.cname_species,
                    t.taxon_rank,
                    0 as agreement_count,
                    false as user_agreed
                FROM burungnesia_identifications bi
                JOIN fobi_users u ON bi.user_id = u.id
                LEFT JOIN taxas t ON t.burnes_fauna_id = bi.taxon_id
                WHERE bi.id = ?
            ", [$identificationId]);

            return response()->json([
                'success' => true,
                'message' => 'Identifikasi berhasil ditambahkan',
                'data' => $identification[0]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in BirdObservationController@addIdentification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan identifikasi'
            ], 500);
        }
    }

    public function agreeWithIdentification($checklistId, $identificationId)
    {
        try {
            // Cek apakah identifikasi ada
            $identification = DB::table('burungnesia_identifications')
                ->where('id', $identificationId)
                ->where('observation_id', $checklistId)
                ->first();

            if (!$identification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifikasi tidak ditemukan'
                ], 404);
            }

            // Cek apakah user sudah pernah agree
            $existingAgreement = DB::table('burungnesia_identifications')
                ->where('agrees_with_id', $identificationId)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingAgreement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah menyetujui identifikasi ini'
                ], 400);
            }

            // Tambah agreement baru
            DB::table('burungnesia_identifications')->insert([
                'observation_id' => $checklistId,
                'observation_type' => 'burungnesia',
                'agrees_with_id' => $identificationId,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Hitung jumlah agreement terbaru
            $agreementCount = DB::table('burungnesia_identifications')
                ->where('agrees_with_id', $identificationId)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyetujui identifikasi',
                'data' => [
                    'agreement_count' => $agreementCount,
                    'user_agreed' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in BirdObservationController@agreeWithIdentification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui identifikasi'
            ], 500);
        }
    }

    public function cancelAgreement($checklistId, $identificationId)
    {
        try {
            // Hapus agreement
            $deleted = DB::table('burungnesia_identifications')
                ->where('agrees_with_id', $identificationId)
                ->where('user_id', auth()->id())
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agreement tidak ditemukan'
                ], 404);
            }

            // Hitung jumlah agreement terbaru
            $agreementCount = DB::table('burungnesia_identifications')
                ->where('agrees_with_id', $identificationId)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil membatalkan persetujuan',
                'data' => [
                    'agreement_count' => $agreementCount,
                    'user_agreed' => false
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in BirdObservationController@cancelAgreement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan persetujuan'
            ], 500);
        }
    }

    public function withdrawIdentification($checklistId, $identificationId)
    {
        try {
            // Cek apakah identifikasi milik user yang sedang login
            $identification = DB::table('burungnesia_identifications')
                ->where('id', $identificationId)
                ->where('observation_id', $checklistId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$identification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifikasi tidak ditemukan atau bukan milik Anda'
                ], 404);
            }

            // Update status withdrawn
            DB::table('burungnesia_identifications')
                ->where('id', $identificationId)
                ->update([
                    'is_withdrawn' => true,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Identifikasi berhasil ditarik'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in BirdObservationController@withdrawIdentification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menarik identifikasi'
            ], 500);
        }
    }

    public function disagreeWithIdentification(Request $request, $checklistId, $identificationId)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:1000',
                'taxon_id' => 'required|exists:taxas,burnes_fauna_id'
            ]);

            // Cek apakah identifikasi ada
            $identification = DB::table('burungnesia_identifications')
                ->where('id', $identificationId)
                ->where('observation_id', $checklistId)
                ->first();

            if (!$identification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifikasi tidak ditemukan'
                ], 404);
            }

            // Tambah identifikasi baru dengan referensi ke identifikasi yang tidak disetujui
            DB::table('burungnesia_identifications')->insert([
                'observation_id' => $checklistId,
                'observation_type' => 'burungnesia',
                'taxon_id' => $request->taxon_id,
                'user_id' => auth()->id(),
                'comment' => $request->comment,
                'disagrees_with_id' => $identificationId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan identifikasi yang berbeda'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in BirdObservationController@disagreeWithIdentification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan ketidaksetujuan'
            ], 500);
        }
    }
}
