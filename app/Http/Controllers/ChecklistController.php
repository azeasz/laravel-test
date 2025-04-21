<?php

// app/Http/Controllers/ChecklistController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ChecklistController extends Controller
{
    public function getGeoJson()
    {
        $user = Auth::user();
        $checklists = Checklist::where('user_id', $user->id)->get();

        $features = $checklists->map(function ($checklist) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        $checklist->longitude,
                        $checklist->latitude,
                    ],
                ],
                'properties' => [
                    'id' => $checklist->id,
                    'observer' => $checklist->observer,
                    'additional_note' => $checklist->additional_note,
                    'tgl_pengamatan' => $checklist->tgl_pengamatan,
                    'start_time' => $checklist->start_time,
                    'end_time' => $checklist->end_time,
                    'tujuan_pengamatan' => $checklist->tujuan_pengamatan,
                    'completed' => $checklist->completed,
                    'can_edit' => $checklist->can_edit,
                ],
            ];
        });

        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];

        return response()->json($geoJson);
    }

    public function show($id)
    {
        $checklists = [
            (object) [
                'id' => $id,
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'source' => 'dummy_source',
                'created_at' => now(),
            ],
        ];

        return view('detail_identifikasi', compact('checklists'));
    }
    public function showSource($source)
{
    if ($source === 'burungnesia') {
        $checklists = DB::connection('second')->table('checklist_fauna')->get();
    } elseif ($source === 'kupunesia') {
        $checklists = DB::connection('third')->table('checklist_fauna')->get();
    } else {
        abort(404, 'Source not found');
    }
    return view('checklist.detail', compact('checklists', 'source'));
}
}
