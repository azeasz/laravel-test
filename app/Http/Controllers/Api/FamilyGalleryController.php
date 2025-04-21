<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyGalleryController extends Controller
{
    public function getFamilyGallery(Request $request)
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
                ->where('t.taxon_rank', 'family')
                ->select(
                    't.id as taxa_id',
                    't.family',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.order_id',
                    't.scientific_name',
                    't.cname_family',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.family',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.order_id',
                    't.scientific_name',
                    't.cname_family',
                    't.description'
                );

            // Filter berdasarkan parent (Order)
            if ($request->has('parent_id')) {
                $parentId = $request->parent_id;
                $order = DB::table('taxas')
                    ->where('id', $parentId)
                    ->value('order');
                
                if ($order) {
                    $query->where('t.order', $order);
                }
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.scientific_name', 'like', "%{$search}%")
                        ->orWhere('t.family', 'like', "%{$search}%")
                        ->orWhere('t.order', 'like', "%{$search}%")
                        ->orWhere('t.cname_family', 'like', "%{$search}%");
                });
            }

            $families = $query->paginate(12);

            // Transform data untuk menambahkan informasi genus
            $familiesData = $families->through(function ($family) {
                // Ambil daftar genus dalam family ini
                $generaInFamily = DB::table('taxas')
                    ->where('family', $family->family)
                    ->where('taxon_rank', 'genus')
                    ->select(
                        'id as taxa_id',
                        'genus',
                        'scientific_name',
                        'description'
                    )
                    ->get();

                // Ambil jumlah pengamatan untuk setiap genus
                $generaWithObservations = $generaInFamily->map(function($genus) {
                    $observationCount = DB::table('fobi_checklist_taxas')
                        ->where('taxa_id', $genus->taxa_id)
                        ->count();

                    return (object) [
                        'taxa_id' => $genus->taxa_id,
                        'genus' => $genus->genus,
                        'scientific_name' => $genus->scientific_name,
                        'description' => $genus->description,
                        'observation_count' => $observationCount
                    ];
                });

                // Tambahkan data genus ke object family
                $family->genus_list = $generaWithObservations;
                $family->genus_count = $generaWithObservations->count();

                return $family;
            });

            // Kembalikan data dengan format yang sesuai untuk infinite scroll
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $familiesData->items(),
                    'current_page' => $families->currentPage(),
                    'last_page' => $families->lastPage(),
                    'per_page' => $families->perPage(),
                    'total' => $families->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getFamilyGallery:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFamilyDetail($taxaId)
    {
        try {
            $family = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.family',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.order_id',
                    't.class_id',
                    't.phylum_id',
                    't.kingdom_id',
                    't.scientific_name',
                    't.cname_family',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category')
                )
                ->first();

            if (!$family) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family tidak ditemukan'
                ], 404);
            }

            // Ambil semua genus dalam family ini
            $genera = DB::table('taxas')
                ->where('family', $family->family)
                ->where('taxon_rank', 'genus')
                ->select('id as taxa_id', 'genus', 'scientific_name', 'cname_genus')
                ->get();

            // Ambil semua media untuk family ini
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
                    'family' => $family,
                    'genera' => $genera,
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

    public function getSimilarFamilies($taxaId)
    {
        try {
            $family = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$family) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family tidak ditemukan'
                ], 404);
            }

            // Cari family lain dalam order yang sama
            $similarFamilies = DB::table('taxas')
                ->where('taxon_rank', 'family')
                ->where('order', $family->order)
                ->where('id', '!=', $taxaId)
                ->select(
                    'id as taxa_id',
                    'family',
                    'order',
                    'class',
                    'phylum',
                    'kingdom',
                    'scientific_name',
                    'cname_family',
                    'description'
                )
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $similarFamilies
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSimilarFamilies:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFamilyDistribution($taxaId)
    {
        try {
            // Dapatkan data family
            $familyData = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'family', 'order', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$familyData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family tidak ditemukan'
                ], 404);
            }

            // Ambil semua genus dalam family ini
            $generaInFamily = DB::table('taxas')
                ->where('family', $familyData->family)
                ->where('taxon_rank', 'genus')
                ->pluck('id');

            // Mengambil semua lokasi observasi dari family ini dan genus di dalamnya
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->distinct()
                ->where(function($query) use ($taxaId, $generaInFamily) {
                    $query->where('fct.taxa_id', $taxaId)
                          ->orWhereIn('fct.taxa_id', $generaInFamily);
                })
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
            \Log::error('Error in getFamilyDistribution:', [
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

    public function getGeneraInFamily($taxaId)
    {
        try {
            // Dapatkan data family
            $family = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'family', 'order', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$family) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family tidak ditemukan'
                ], 404);
            }

            // Ambil semua genus dalam family ini
            $genera = DB::table('taxas')
                ->where('family', $family->family)
                ->where('taxon_rank', 'genus')
                ->select(
                    'id as taxa_id',
                    'genus',
                    'scientific_name',
                    'cname_genus',
                    'description'
                )
                ->get();

            // Ambil data media dan jumlah observasi untuk setiap genus
            $generaWithDetails = $genera->map(function($genus) {
                $observationCount = DB::table('fobi_checklist_taxas')
                    ->where('taxa_id', $genus->taxa_id)
                    ->count();

                $media = DB::table('fobi_checklist_taxas as fct')
                    ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                    ->where('fct.taxa_id', $genus->taxa_id)
                    ->select('fcm.file_path')
                    ->first();

                return (object) [
                    'taxa_id' => $genus->taxa_id,
                    'genus' => $genus->genus,
                    'scientific_name' => $genus->scientific_name,
                    'cname_genus' => $genus->cname_genus,
                    'description' => $genus->description,
                    'observation_count' => $observationCount,
                    'media' => $media ? [$media] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $generaWithDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getGeneraInFamily:', [
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