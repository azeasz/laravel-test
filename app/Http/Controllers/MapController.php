<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenusFauna;

class MapController extends Controller
{
    public function show($genus)
    {
        $genusFauna = GenusFauna::where('genus', $genus)->first();

        if (!$genusFauna) {
            abort(404, 'Genus not found');
        }

        $locations = $genusFauna->checklists()->select('latitude', 'longitude')->get();

        return view('genus.partials.map', compact('genus', 'locations'));
    }

    public function apiMap($genus)
    {
        $genusFauna = GenusFauna::where('genus', $genus)->first();

        if (!$genusFauna) {
            return response()->json(['error' => 'Genus not found'], 404);
        }

        $locations = $genusFauna->checklists()->select('latitude', 'longitude')->get();

        return response()->json(['genus' => $genus, 'locations' => $locations]);
    }
}
