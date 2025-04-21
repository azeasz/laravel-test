<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpeciesController extends Controller
{
    public function getSpeciesInChecklist($checklist_id)
    {
        try {
            // Mengambil data spesies dari database 'second'
            $speciesSecond = DB::connection('second')->table('checklist_fauna')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklist_fauna.checklist_id', $checklist_id)
                ->select('faunas.nameId', 'faunas.nameLat', 'faunas.id')
                ->get();

            // Mengambil data spesies dari database 'third'
            $speciesThird = DB::connection('third')->table('checklist_fauna')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklist_fauna.checklist_id', $checklist_id)
                ->select('faunas.nameId', 'faunas.nameLat', 'faunas.id')
                ->get();

            // Menggabungkan hasil dari kedua database
            $species = $speciesSecond->merge($speciesThird);

            return response()->json($species);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
