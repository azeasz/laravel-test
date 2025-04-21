<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Observation;
use App\Models\Suggestion;
use App\Models\User;

class IdentificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Observation::query();

        if ($request->has('species') && $request->species != '') {
            $query->where('scientific_name', 'like', '%' . $request->species . '%');
        }

        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('quality') && $request->quality != '') {
            $query->where('status', $request->quality);
        }

        $observations = $query->paginate(12);

        return view('identification.index', compact('user', 'observations'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $observation = Observation::findOrFail($id);
        $suggestions = Suggestion::where('observation_id', $id)->get();
        return view('identification.show', compact('user', 'observation', 'suggestions'));
    }

    public function storeSuggestion(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $suggestion = new Suggestion();
        $suggestion->user_id = auth()->id();
        $suggestion->observation_id = $id;
        $suggestion->name = $request->name;
        $suggestion->description = $request->description;
        $suggestion->save();

        return redirect()->route('identification.show', $id)->with('success', 'Usulan nama berhasil ditambahkan.');
    }
}
