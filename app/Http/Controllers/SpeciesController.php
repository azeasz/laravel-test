<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SpeciesController extends Controller
{
    public function getSpecies($checklist_id, Request $request)
    {
        try {
            $perPage = $request->input('per_page', 7); // Default 7 items per page
            $page = $request->input('page', 1);

            $cacheKey = "species_{$checklist_id}_page_{$page}_per_page_{$perPage}";
            $speciesData = Cache::remember($cacheKey, 300, function() use ($checklist_id, $perPage, $page) {
                $offset = ($page - 1) * $perPage;

                $burungnesiaSpecies = DB::connection('second')->table('checklist_fauna')
                    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                    ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
                    ->where('checklist_fauna.checklist_id', $checklist_id)
                    ->select('faunas.nameId', 'faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'faunas.id')
                    ->groupBy('faunas.nameId', 'faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'faunas.id')
                    ->offset($offset)
                    ->limit($perPage)
                    ->get();

                $kupunesiaSpecies = DB::connection('third')->table('checklist_fauna')
                    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                    ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
                    ->where('checklist_fauna.checklist_id', $checklist_id)
                    ->select('faunas.nameId', 'faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'faunas.id')
                    ->groupBy('faunas.nameId', 'faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'faunas.id')
                    ->offset($offset)
                    ->limit($perPage)
                    ->get();

                return [
                    'burungnesia' => $burungnesiaSpecies,
                    'kupunesia' => $kupunesiaSpecies,
                ];
            });

            return response()->json($speciesData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
public function showSpeciesDetail($fauna_id, Request $request)
{
    try {
        // Mengambil detail spesies dari database 'second' (burungnesia)
        $burungnesiaDetail = DB::connection('second')->table('checklist_fauna')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
            ->where('faunas.id', $fauna_id)
            ->select('faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'checklists.media_path')
            ->first();

        // Mengambil detail spesies dari database 'third' (kupunesia)
        $kupunesiaDetail = DB::connection('third')->table('checklist_fauna')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
            ->where('faunas.id', $fauna_id)
            ->select('faunas.nameLat', 'checklists.latitude', 'checklists.longitude', 'checklists.media_path')
            ->first();

        // Memeriksa apakah data ditemukan di salah satu database
        if (!$burungnesiaDetail && !$kupunesiaDetail) {
            return response()->json(['error' => 'Species not found'], 404);
        }

        // Menggabungkan hasil dari kedua database
        $speciesDetail = $burungnesiaDetail ?: $kupunesiaDetail;

        return view('species.detail', ['species' => $speciesDetail]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}}
