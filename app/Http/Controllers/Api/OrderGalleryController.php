<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderGalleryController extends Controller
{
    public function getOrderGallery(Request $request)
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
                ->where('t.taxon_rank', 'order')
                ->select(
                    't.id as taxa_id',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.class_id',
                    't.scientific_name',
                    't.cname_order',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.class_id',
                    't.scientific_name',
                    't.cname_order',
                    't.description'
                );

            // Filter berdasarkan parent (Class)
            if ($request->has('parent_id')) {
                $parentId = $request->parent_id;
                $class = DB::table('taxas')
                    ->where('id', $parentId)
                    ->value('class');
                
                if ($class) {
                    $query->where('t.class', $class);
                }
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.scientific_name', 'like', "%{$search}%")
                        ->orWhere('t.order', 'like', "%{$search}%")
                        ->orWhere('t.class', 'like', "%{$search}%")
                        ->orWhere('t.cname_order', 'like', "%{$search}%");
                });
            }

            $orders = $query->paginate(12);

            // Transform data untuk menambahkan informasi family
            $ordersData = $orders->through(function ($order) {
                // Ambil daftar family dalam order ini
                $familiesInOrder = DB::table('taxas')
                    ->where('order', $order->order)
                    ->where('taxon_rank', 'family')
                    ->select(
                        'id as taxa_id',
                        'family',
                        'scientific_name',
                        'description'
                    )
                    ->get();

                // Ambil jumlah pengamatan untuk setiap family
                $familiesWithObservations = $familiesInOrder->map(function($family) {
                    $observationCount = DB::table('fobi_checklist_taxas')
                        ->where('taxa_id', $family->taxa_id)
                        ->count();

                    return (object) [
                        'taxa_id' => $family->taxa_id,
                        'family' => $family->family,
                        'scientific_name' => $family->scientific_name,
                        'description' => $family->description,
                        'observation_count' => $observationCount
                    ];
                });

                // Tambahkan data family ke object order
                $order->family_list = $familiesWithObservations;
                $order->family_count = $familiesWithObservations->count();

                return $order;
            });

            // Kembalikan data dengan format yang sesuai untuk infinite scroll
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $ordersData->items(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getOrderGallery:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderDetail($taxaId)
    {
        try {
            $order = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.order',
                    't.class',
                    't.phylum',
                    't.kingdom',
                    't.class_id',
                    't.phylum_id',
                    't.kingdom_id',
                    't.scientific_name',
                    't.cname_order',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category')
                )
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Ambil semua family dalam order ini
            $families = DB::table('taxas')
                ->where('order', $order->order)
                ->where('taxon_rank', 'family')
                ->select('id as taxa_id', 'family', 'scientific_name', 'cname_family')
                ->get();

            // Ambil semua media untuk order ini
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
                    'order' => $order,
                    'families' => $families,
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

    public function getSimilarOrders($taxaId)
    {
        try {
            $order = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Cari order lain dalam class yang sama
            $similarOrders = DB::table('taxas')
                ->where('taxon_rank', 'order')
                ->where('class', $order->class)
                ->where('id', '!=', $taxaId)
                ->select(
                    'id as taxa_id',
                    'order',
                    'class',
                    'phylum',
                    'kingdom',
                    'scientific_name',
                    'cname_order',
                    'description'
                )
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $similarOrders
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSimilarOrders:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderDistribution($taxaId)
    {
        try {
            // Dapatkan data order
            $orderData = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'order', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$orderData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Ambil semua family dalam order ini
            $familiesInOrder = DB::table('taxas')
                ->where('order', $orderData->order)
                ->where('taxon_rank', 'family')
                ->pluck('id');

            // Mengambil semua lokasi observasi dari order ini dan family di dalamnya
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->distinct()
                ->where(function($query) use ($taxaId, $familiesInOrder) {
                    $query->where('fct.taxa_id', $taxaId)
                          ->orWhereIn('fct.taxa_id', $familiesInOrder);
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
            \Log::error('Error in getOrderDistribution:', [
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

    public function getFamiliesInOrder($taxaId)
    {
        try {
            // Dapatkan data order
            $order = DB::table('taxas')
                ->where('id', $taxaId)
                ->select('id', 'order', 'class', 'phylum', 'kingdom', 'scientific_name')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Ambil semua family dalam order ini
            $families = DB::table('taxas')
                ->where('order', $order->order)
                ->where('taxon_rank', 'family')
                ->select(
                    'id as taxa_id',
                    'family',
                    'scientific_name',
                    'cname_family',
                    'description'
                )
                ->get();

            // Ambil data media dan jumlah observasi untuk setiap family
            $familiesWithDetails = $families->map(function($family) {
                $observationCount = DB::table('fobi_checklist_taxas')
                    ->where('taxa_id', $family->taxa_id)
                    ->count();

                $media = DB::table('fobi_checklist_taxas as fct')
                    ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                    ->where('fct.taxa_id', $family->taxa_id)
                    ->select('fcm.file_path')
                    ->first();

                return (object) [
                    'taxa_id' => $family->taxa_id,
                    'family' => $family->family,
                    'scientific_name' => $family->scientific_name,
                    'cname_family' => $family->cname_family,
                    'description' => $family->description,
                    'observation_count' => $observationCount,
                    'media' => $media ? [$media] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $familiesWithDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getFamiliesInOrder:', [
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