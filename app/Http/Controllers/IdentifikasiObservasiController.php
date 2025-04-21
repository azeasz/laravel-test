<?php

namespace App\Http\Controllers;

use App\Models\IdentifikasiObservasi;
use Illuminate\Http\Request;

class IdentifikasiObservasiController extends Controller
{
    public function index()
    {
        $observations = IdentifikasiObservasi::with('suggestions', 'approvals')->get();
        return view('faunas.index', compact('observations'));
    }
    public function showExif($id)
    {
        $observation = IdentifikasiObservasi::findOrFail($id);
        // Logika untuk menampilkan data Exif
        return view('faunas.exif', compact('observation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'species_name' => 'required|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'observed_at' => 'required|date',
            'uploaded_at' => 'required|date',
        ]);

        IdentifikasiObservasi::create($request->all());

        return redirect()->route('faunas.index')->with('success', 'Observasi berhasil ditambahkan!');
    }
}
