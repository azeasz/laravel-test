<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fauna;
use App\Models\Checklist;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $species = $request->input('species', 'Limosa limosa'); // Default ke 'Limosa limosa' jika tidak ada input
        $faunas = Fauna::where('nameLat', $species)->with('checklists')->get();

        // Data dummy untuk contoh, ganti dengan data dari database
        $data = [
            'species' => $species,
            'observations' => 1023,
            'media' => 100,
            'species_tree' => 10,
            'contributors' => 1,
            'checklist_kupnesia' => 0,
            'checklist_burnes' => 1,
            'gallery_items' => [
                ['src' => 'storage/icon/blt.jpeg', 'alt' => 'Observasi 1'],
                ['src' => 'storage/icon/blt.jpeg', 'alt' => 'Observasi 2'],
                // Tambahkan item galeri lainnya di sini
            ],
            'similar_species' => [
                ['src' => 'storage/icon/blt.jpeg', 'alt' => 'Limosa harlequin'],
                ['src' => 'storage/icon/blt.jpeg', 'alt' => 'Limosa hutchinsii'],
            ],
            'map_src' => 'path/to/map.jpg',
        ];

        return view('faunas.gallery', $data, compact('faunas'));
    }
}
