<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = strtolower($request->query('q', ''));

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'suggestions' => []
                ]);
            }

            // Cari nama spesies dari FOBI
            $fobiSpecies = DB::table('fobi_checklist_taxas as fct')
                ->join('taxas as t', 't.id', '=', 'fct.taxa_id')
                ->where('t.scientific_name', 'like', "%{$query}%")
                ->orWhere('t.cname_species', 'like', "%{$query}%")
                ->select(
                    't.scientific_name',
                    't.cname_species as common_name',
                    DB::raw("'species' as type")
                )
                ->distinct()
                ->limit(5)
                ->get();

            // Cari lokasi dari FOBI
            $fobiLocations = DB::table('fobi_checklist_taxas')
                ->where('location', 'like', "%{$query}%")
                ->select(
                    'location as name',
                    DB::raw("'location' as type")
                )
                ->distinct()
                ->limit(5)
                ->get();

            // Gabungkan semua suggestions
            $suggestions = collect($fobiSpecies)
                ->concat($fobiLocations)
                ->take(10)
                ->values();

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in search:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian'
            ], 500);
        }
    }
}
