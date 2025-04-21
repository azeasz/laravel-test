<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpeciesSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q');
        $source = $request->query('source', 'burungnesia');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        try {
            if ($source === 'kupunesia') {
                // Pencarian untuk Kupunesia dari tabel faunas_kupnes
                $species = DB::table('faunas_kupnes')
                    ->select(['id', 'nameId', 'nameLat', 'family'])
                    ->where(function($q) use ($query) {
                        $q->where(DB::raw('REPLACE(nameId, "-", " ")'), 'like', "%{$query}%")
                          ->orWhere(DB::raw('REPLACE(nameLat, "-", " ")'), 'like', "%{$query}%");
                    })
                    ->whereNotNull('nameId')
                    ->whereNotNull('nameLat')
                    ->limit(10)
                    ->get();
            } else {
                // Pencarian untuk Burungnesia dari tabel faunas
                $species = DB::table('faunas')
                    ->select(['id', 'nameId', 'nameLat', 'family'])
                    ->where(function($q) use ($query) {
                        $q->where(DB::raw('REPLACE(nameId, "-", " ")'), 'like', "%{$query}%")
                          ->orWhere(DB::raw('REPLACE(nameLat, "-", " ")'), 'like', "%{$query}%");
                    })
                    ->whereNotNull('nameId')
                    ->whereNotNull('nameLat')
                    ->limit(10)
                    ->get();
            }

            return response()->json([
                'data' => $species
            ]);

        } catch (\Exception $e) {
            \Log::error("Species search error:", [
                'message' => $e->getMessage(),
                'source' => $source,
                'query' => $query
            ]);

            return response()->json([
                'error' => 'Gagal mencari spesies: ' . $e->getMessage()
            ], 500);
        }
    }
}
