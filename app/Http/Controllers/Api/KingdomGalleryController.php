<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KingdomGalleryController extends Controller
{
    public function getKingdomGallery(Request $request)
    {
        try {
            // Query utama dimulai dari tabel taxas
            $query = DB::table('taxas as t')
                ->leftJoin('fobi_checklist_taxas as fct', function($join) {
                    $join->on('fct.taxa_id', '=', 't.id')
                         ->orWhereRaw('t.burnes_fauna_id = fct.taxa_id')
                         ->orWhereRaw('t.kupnes_fauna_id = fct.taxa_id');
                })
                ->leftJoin('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->where('t.taxon_rank', 'kingdom')
                ->select(
                    't.id as taxa_id',
                    't.kingdom',
                    't.scientific_name',
                    't.cname_kingdom',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.kingdom',
                    't.scientific_name',
                    't.cname_kingdom',
                    't.description'
                );

            // Pencarian berdasarkan parameter search atau name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.scientific_name', 'like', "%{$search}%")
                        ->orWhere('t.kingdom', 'like', "%{$search}%")
                        ->orWhere('t.cname_kingdom', 'like', "%{$search}%");
                });
            } elseif ($request->has('name')) {
                $name = $request->name;
                $query->where(function($q) use ($name) {
                    $q->where('t.scientific_name', 'like', "%{$name}%")
                        ->orWhere('t.kingdom', 'like', "%{$name}%")
                        ->orWhere('t.cname_kingdom', 'like', "%{$name}%");
                });
            }

            // Debugging Kingdom Animalia - pastikan Kingdom Animalia selalu muncul
            \Log::info('Debugging Kingdom Query', [
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            // Cek apakah Kingdom Animalia ada dalam database sebelum pagination
            $animaliaExists = DB::table('taxas')
                ->where('taxon_rank', 'kingdom')
                ->where(function($q) {
                    $q->where('kingdom', 'like', '%Animalia%')
                      ->orWhere('scientific_name', 'like', '%Animalia%');
                })
                ->exists();

            \Log::info('Apakah Kingdom Animalia ada?', ['exists' => $animaliaExists]);

            // Lakukan pagination
            $kingdoms = $query->paginate(12);

            // Transform data untuk menambahkan informasi phylum
            $kingdomsData = $kingdoms->through(function ($kingdom) {
                // Ambil daftar phylum dalam kingdom ini
                $phylaInKingdom = DB::table('taxas')
                    ->where('kingdom', $kingdom->kingdom)
                    ->where('taxon_rank', 'phylum')
                    ->select(
                        'id as taxa_id',
                        'phylum',
                        'scientific_name',
                        'description'
                    )
                    ->get();

                // Ambil jumlah pengamatan untuk setiap phylum
                $phylaWithObservations = $phylaInKingdom->map(function($phylum) {
                    $observationCount = DB::table('fobi_checklist_taxas')
                        ->where('taxa_id', $phylum->taxa_id)
                        ->count();

                    return (object) [
                        'taxa_id' => $phylum->taxa_id,
                        'phylum' => $phylum->phylum,
                        'scientific_name' => $phylum->scientific_name,
                        'description' => $phylum->description,
                        'observation_count' => $observationCount
                    ];
                });

                // Tambahkan data phylum ke object kingdom
                $kingdom->phylum_list = $phylaWithObservations;
                $kingdom->phylum_count = $phylaWithObservations->count();

                return $kingdom;
            });

            // Kembalikan data dengan format yang sesuai untuk infinite scroll
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $kingdomsData->items(),
                    'current_page' => $kingdoms->currentPage(),
                    'last_page' => $kingdoms->lastPage(),
                    'per_page' => $kingdoms->perPage(),
                    'total' => $kingdoms->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKingdomGallery:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKingdomDetail($taxaId)
    {
        try {
            $kingdom = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.kingdom',
                    't.scientific_name',
                    't.cname_kingdom',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category')
                )
                ->first();

            if (!$kingdom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kingdom tidak ditemukan'
                ], 404);
            }

            // Ambil semua phylum dalam kingdom ini
            $phyla = DB::table('taxas')
                ->where('kingdom', $kingdom->kingdom)
                ->where('taxon_rank', 'phylum')
                ->select('id as taxa_id', 'phylum', 'scientific_name', 'cname_phylum')
                ->get();

            // Ambil semua media untuk kingdom ini
            $media = DB::table('fobi_checklist_taxas as fct')
                ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->where('fct.taxa_id', $taxaId)
                ->select(
                    'fcm.id',
                    'fcm.file_path',
                    'fcm.spectrogram',
                    'fcm.habitat',
                    'fcm.location',
                    'fcm.date',
                    'fcm.description as observation_notes'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'kingdom' => $kingdom,
                    'phyla' => $phyla,
                    'media' => $media
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    public function getSimilarKingdoms($taxaId)
    {
        try {
            $kingdom = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$kingdom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kingdom tidak ditemukan'
                ], 404);
            }

            // Cari kingdom lain selain yang diminta
            $similarKingdoms = DB::table('taxas')
                ->where('taxon_rank', 'kingdom')
                ->where('id', '!=', $taxaId)
                ->select(
                    'id as taxa_id',
                    'kingdom',
                    'scientific_name',
                    'cname_kingdom',
                    'description'
                )
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $similarKingdoms
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSimilarKingdoms:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKingdomDistribution($taxaId)
    {
        try {
            // Dapatkan data kingdom
            $kingdomData = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'kingdom', 'scientific_name')
                ->first();

            if (!$kingdomData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kingdom tidak ditemukan'
                ], 404);
            }

            // Ambil semua phylum dalam kingdom ini
            $phylaInKingdom = DB::table('taxas')
                ->where('kingdom', $kingdomData->kingdom)
                ->where('taxon_rank', 'phylum')
                ->pluck('id');

            // Mengambil semua lokasi observasi dari phylum dalam kingdom ini
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->distinct()
                ->whereIn('fct.taxa_id', $phylaInKingdom)
                ->whereNotNull('fct.latitude')
                ->whereNotNull('fct.longitude')
                ->select(
                    'fct.latitude',
                    'fct.longitude',
                    'fct.id',
                    DB::raw("'fobi' as source")
                )
                ->get()
                ->map(function($item) {
                    return [
                        'latitude' => (float) $item->latitude,
                        'longitude' => (float) $item->longitude,
                        'id' => 'fobi_' . $item->id,
                        'source' => $item->source
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKingdomDistribution:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPhylaInKingdom($taxaId)
    {
        try {
            // Dapatkan data kingdom
            $kingdom = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'kingdom', 'scientific_name')
                ->first();

            if (!$kingdom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kingdom tidak ditemukan'
                ], 404);
            }

            // Ambil semua phylum dalam kingdom ini
            $phyla = DB::table('taxas')
                ->where('kingdom', $kingdom->kingdom)
                ->where('taxon_rank', 'phylum')
                ->select(
                    'id as taxa_id',
                    'phylum',
                    'scientific_name',
                    'cname_phylum',
                    'description'
                )
                ->get();

            // Ambil data media dan jumlah observasi untuk setiap phylum
            $phylaWithDetails = $phyla->map(function($phylum) {
                $observationCount = DB::table('fobi_checklist_taxas')
                    ->where('taxa_id', $phylum->taxa_id)
                    ->count();

                $media = DB::table('fobi_checklist_taxas as fct')
                    ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                    ->where('fct.taxa_id', $phylum->taxa_id)
                    ->select('fcm.file_path')
                    ->first();

                return (object) [
                    'taxa_id' => $phylum->taxa_id,
                    'phylum' => $phylum->phylum,
                    'scientific_name' => $phylum->scientific_name,
                    'cname_phylum' => $phylum->cname_phylum,
                    'description' => $phylum->description,
                    'observation_count' => $observationCount,
                    'media' => $media ? [$media] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $phylaWithDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getPhylaInKingdom:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 