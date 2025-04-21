<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taxontest;
use App\Models\GenusFauna;
use Illuminate\Support\Facades\DB;
class GenusGalleryController extends Controller
{
    public function show($genus)
    {
        // Cek apakah genus adalah bagian dari Aves
        $avesGenus = GenusFauna::where('genus', $genus)->first();

        if ($avesGenus) {
            // Ambil data dari tabel genus_faunas
            $species = GenusFauna::where('genus', $genus)->get();
        } else {
            // Ambil data dari tabel taxontests
            $species = Taxontest::where('genus', $genus)->get();
        }

        return view('genus.gallery', compact('genus', 'species', 'avesGenus'));
    }
}
