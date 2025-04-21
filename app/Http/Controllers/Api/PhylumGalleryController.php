<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhylumGalleryController extends Controller
{
    public function getPhylumGallery(Request $request)
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
                ->where('t.taxon_rank', 'phylum')
                ->select(
                    't.id as taxa_id',
                    't.phylum',
                    't.kingdom',
                    't.scientific_name',
                    't.cname_phylum',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.phylum',
                    't.kingdom',
                    't.scientific_name',
                    't.cname_phylum',
                    't.description'
                );

            // Filter berdasarkan parent (Kingdom)
            if ($request->has('parent_id')) {
                $parentId = $request->parent_id;
                $kingdom = DB::table('taxas')
                    ->where('id', $parentId)
                    ->value('kingdom');
                
                if ($kingdom) {
                    $query->where('t.kingdom', $kingdom);
                }
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.scientific_name', 'like', "%{$search}%")
                        ->orWhere('t.phylum', 'like', "%{$search}%")
                        ->orWhere('t.kingdom', 'like', "%{$search}%")
                        ->orWhere('t.cname_phylum', 'like', "%{$search}%");
                });
            }

            $phyla = $query->paginate(12);

            // Transform data untuk menambahkan informasi class
            $phylaData = $phyla->through(function ($phylum) {
                // Ambil daftar class dalam phylum ini
                $classesInPhylum = DB::table('taxas')
                    ->where('phylum', $phylum->phylum)
                    ->where('taxon_rank', 'class')
                    ->select(
                        'id as taxa_id',
                        'class',
                        'scientific_name',
                        'description'
                    )
                    ->get();

                // Ambil jumlah pengamatan untuk setiap class
                $classesWithObservations = $classesInPhylum->map(function($class) {
                    $observationCount = DB::table('fobi_checklist_taxas')
                        ->where('taxa_id', $class->taxa_id)
                        ->count();

                    return (object) [
                        'taxa_id' => $class->taxa_id,
                        'class' => $class->class,
                        'scientific_name' => $class->scientific_name,
                        'description' => $class->description,
                        'observation_count' => $observationCount
                    ];
                });

                // Tambahkan data class ke object phylum
                $phylum->class_list = $classesWithObservations;
                $phylum->class_count = $classesWithObservations->count();

                return $phylum;
            });

            // Kembalikan data dengan format yang sesuai untuk infinite scroll
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $phylaData->items(),
                    'current_page' => $phyla->currentPage(),
                    'last_page' => $phyla->lastPage(),
                    'per_page' => $phyla->perPage(),
                    'total' => $phyla->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getPhylumGallery:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPhylumDetail($taxaId)
    {
        try {
            $phylum = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.phylum',
                    't.kingdom',
                    't.kingdom_id',
                    't.scientific_name',
                    't.cname_phylum',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category')
                )
                ->first();

            if (!$phylum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phylum tidak ditemukan'
                ], 404);
            }

            // Ambil semua class dalam phylum ini
            $classes = DB::table('taxas')
                ->where('phylum', $phylum->phylum)
                ->where('taxon_rank', 'class')
                ->select('id as taxa_id', 'class', 'scientific_name', 'cname_class')
                ->get();

            // Ambil semua media untuk phylum ini
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
                    'phylum' => $phylum,
                    'classes' => $classes,
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

    public function getSimilarPhyla($taxaId)
    {
        try {
            $phylum = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$phylum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phylum tidak ditemukan'
                ], 404);
            }

            // Cari phyla dalam kingdom yang sama
            $similarPhyla = DB::table('taxas')
                ->where('taxon_rank', 'phylum')
                ->where('kingdom', $phylum->kingdom)
                ->where('id', '!=', $taxaId)
                ->select(
                    'id as taxa_id',
                    'phylum',
                    'kingdom',
                    'scientific_name',
                    'cname_phylum',
                    'description'
                )
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $similarPhyla
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSimilarPhyla:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPhylumDistribution($taxaId)
    {
        try {
            // Dapatkan data phylum
            $phylumData = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$phylumData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phylum tidak ditemukan'
                ], 404);
            }

            // Ambil semua class dalam phylum ini
            $classesInPhylum = DB::table('taxas')
                ->where('phylum', $phylumData->phylum)
                ->where('taxon_rank', 'class')
                ->pluck('id');

            // Mengambil semua lokasi observasi dari phylum ini dan class di dalamnya
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->distinct()
                ->where(function($query) use ($taxaId, $classesInPhylum) {
                    $query->where('fct.taxa_id', $taxaId)
                          ->orWhereIn('fct.taxa_id', $classesInPhylum);
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
            \Log::error('Error in getPhylumDistribution:', [
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

    public function getClassesInPhylum($taxaId)
    {
        try {
            // Dapatkan data phylum
            $phylum = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$phylum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phylum tidak ditemukan'
                ], 404);
            }

            // Ambil semua class dalam phylum ini
            $classes = DB::table('taxas')
                ->where('phylum', $phylum->phylum)
                ->where('taxon_rank', 'class')
                ->select(
                    'id as taxa_id',
                    'class',
                    'scientific_name',
                    'cname_class',
                    'description'
                )
                ->get();

            // Ambil data media dan jumlah observasi untuk setiap class
            $classesWithDetails = $classes->map(function($class) {
                $observationCount = DB::table('fobi_checklist_taxas')
                    ->where('taxa_id', $class->taxa_id)
                    ->count();

                $media = DB::table('fobi_checklist_taxas as fct')
                    ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                    ->where('fct.taxa_id', $class->taxa_id)
                    ->select('fcm.file_path')
                    ->first();

                return (object) [
                    'taxa_id' => $class->taxa_id,
                    'class' => $class->class,
                    'scientific_name' => $class->scientific_name,
                    'cname_class' => $class->cname_class,
                    'description' => $class->description,
                    'observation_count' => $observationCount,
                    'media' => $media ? [$media] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $classesWithDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getClassesInPhylum:', [
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