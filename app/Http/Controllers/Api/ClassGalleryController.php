<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassGalleryController extends Controller
{
    public function getClassGallery(Request $request)
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
                ->where('t.taxon_rank', 'class')
                ->select(
                    't.id as taxa_id',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.phylum_id',
                    't.scientific_name',
                    't.cname_class',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.phylum_id',
                    't.scientific_name',
                    't.cname_class',
                    't.description'
                );

            // Filter berdasarkan parent (Phylum)
            if ($request->has('parent_id')) {
                $parentId = $request->parent_id;
                $phylum = DB::table('taxas')
                    ->where('id', $parentId)
                    ->value('phylum');
                
                if ($phylum) {
                    $query->where('t.phylum', $phylum);
                }
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.scientific_name', 'like', "%{$search}%")
                        ->orWhere('t.class', 'like', "%{$search}%")
                        ->orWhere('t.phylum', 'like', "%{$search}%")
                        ->orWhere('t.cname_class', 'like', "%{$search}%");
                });
            }

            $classes = $query->paginate(12);

            // Transform data untuk menambahkan informasi order
            $classesData = $classes->through(function ($class) {
                // Ambil daftar order dalam class ini
                $ordersInClass = DB::table('taxas')
                    ->where('class', $class->class)
                    ->where('taxon_rank', 'order')
                    ->select(
                        'id as taxa_id',
                        'order',
                        'scientific_name',
                        'description'
                    )
                    ->get();

                // Ambil jumlah pengamatan untuk setiap order
                $ordersWithObservations = $ordersInClass->map(function($order) {
                    $observationCount = DB::table('fobi_checklist_taxas')
                        ->where('taxa_id', $order->taxa_id)
                        ->count();

                    return (object) [
                        'taxa_id' => $order->taxa_id,
                        'order' => $order->order,
                        'scientific_name' => $order->scientific_name,
                        'description' => $order->description,
                        'observation_count' => $observationCount
                    ];
                });

                // Tambahkan data order ke object class
                $class->order_list = $ordersWithObservations;
                $class->order_count = $ordersWithObservations->count();

                return $class;
            });

            // Kembalikan data dengan format yang sesuai untuk infinite scroll
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $classesData->items(),
                    'current_page' => $classes->currentPage(),
                    'last_page' => $classes->lastPage(),
                    'per_page' => $classes->perPage(),
                    'total' => $classes->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getClassGallery:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClassDetail($taxaId)
    {
        try {
            $class = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.phylum_id',
                    't.kingdom_id',
                    't.scientific_name',
                    't.cname_class',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category')
                )
                ->first();

            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class tidak ditemukan'
                ], 404);
            }

            // Ambil semua order dalam class ini
            $orders = DB::table('taxas')
                ->where('class', $class->class)
                ->where('taxon_rank', 'order')
                ->select('id as taxa_id', 'order', 'scientific_name', 'cname_order')
                ->get();

            // Ambil semua media untuk class ini
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
                    'class' => $class,
                    'orders' => $orders,
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

    public function getSimilarClasses($taxaId)
    {
        try {
            $class = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class tidak ditemukan'
                ], 404);
            }

            // Cari class lain dalam phylum yang sama
            $similarClasses = DB::table('taxas')
                ->where('taxon_rank', 'class')
                ->where('phylum', $class->phylum)
                ->where('id', '!=', $taxaId)
                ->select(
                    'id as taxa_id',
                    'class',
                    'phylum',
                    'kingdom',
                    'scientific_name',
                    'cname_class',
                    'description'
                )
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $similarClasses
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSimilarClasses:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClassDistribution($taxaId)
    {
        try {
            // Dapatkan data class
            $classData = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$classData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class tidak ditemukan'
                ], 404);
            }

            // Ambil semua order dalam class ini
            $ordersInClass = DB::table('taxas')
                ->where('class', $classData->class)
                ->where('taxon_rank', 'order')
                ->pluck('id');

            // Mengambil semua lokasi observasi dari class ini dan order di dalamnya
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->distinct()
                ->where(function($query) use ($taxaId, $ordersInClass) {
                    $query->where('fct.taxa_id', $taxaId)
                          ->orWhereIn('fct.taxa_id', $ordersInClass);
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
            \Log::error('Error in getClassDistribution:', [
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

    public function getOrdersInClass($taxaId)
    {
        try {
            // Dapatkan data class
            $class = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class tidak ditemukan'
                ], 404);
            }

            // Ambil semua order dalam class ini
            $orders = DB::table('taxas')
                ->where('class', $class->class)
                ->where('taxon_rank', 'order')
                ->select(
                    'id as taxa_id',
                    'order',
                    'scientific_name',
                    'cname_order',
                    'description'
                )
                ->get();

            // Ambil data media dan jumlah observasi untuk setiap order
            $ordersWithDetails = $orders->map(function($order) {
                $observationCount = DB::table('fobi_checklist_taxas')
                    ->where('taxa_id', $order->taxa_id)
                    ->count();

                $media = DB::table('fobi_checklist_taxas as fct')
                    ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                    ->where('fct.taxa_id', $order->taxa_id)
                    ->select('fcm.file_path')
                    ->first();

                return (object) [
                    'taxa_id' => $order->taxa_id,
                    'order' => $order->order,
                    'scientific_name' => $order->scientific_name,
                    'cname_order' => $order->cname_order,
                    'description' => $order->description,
                    'observation_count' => $observationCount,
                    'media' => $media ? [$media] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $ordersWithDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getOrdersInClass:', [
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