<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PolygonSidebarController extends Controller 
{
    public function getPolygonData(Request $request)
    {
        try {
            $shape = $request->input('shape');
            $page = $request->input('page', 1);
            $perPage = 5;
            $offset = ($page - 1) * $perPage;

            if (!$shape) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shape data is required'
                ], 400);
            }

            // Gunakan Cache untuk menyimpan hasil query
            $cacheKey = 'polygon_data_' . md5(json_encode($shape) . $page);
            $cacheDuration = 60; // 1 menit

            return Cache::remember($cacheKey, $cacheDuration, function() use ($shape, $perPage, $offset) {
                // Query untuk setiap sumber data dengan pagination
                $result = $this->getDataFromAllSources($shape, $perPage, $offset);
                
                return response()->json([
                    'status' => 'success',
                    'data' => $result['data'],
                    'hasMore' => $result['hasMore'],
                    'total' => $result['total']
                ]);
            });

        } catch (\Exception $e) {
            \Log::error('Error in getPolygonData: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getDataFromAllSources($shape, $perPage, $offset)
    {
        // Implementasi query untuk setiap sumber data
        $queries = $this->buildQueries($shape);
        
        // Hitung total data
        $total = $this->getTotalCount($queries);
        
        // Ambil data dengan pagination
        $data = $this->getPaginatedData($queries, $perPage, $offset);
        
        return [
            'data' => $data,
            'hasMore' => ($offset + $perPage) < $total,
            'total' => $total
        ];
    }

    // Implementasi method helper lainnya...
} 