<?php

namespace App\Http\Controllers;

use App\Models\TaxaAnimalia;
use Illuminate\Http\Request;

class TaxaAnimaliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pagination dari request, default 15
        $perPage = $request->get('per_page', 15);

        // Ambil data dengan pagination
        $taxa = TaxaAnimalia::paginate($perPage);

        // Kembalikan data dalam format JSON dengan informasi tambahan
        return response()->json([
            'data' => $taxa->items(),
            'total_results' => $taxa->total(),
            'total_pages' => $taxa->lastPage(),
            'current_page' => $taxa->currentPage(),
            'per_page' => $taxa->perPage(),
        ]);
    }
}
