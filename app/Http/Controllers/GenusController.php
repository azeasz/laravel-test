<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taxontest;

class GenusController extends Controller
{
    public function show($genus)
    {
        $genusData = Taxontest::where('genus', $genus)->firstOrFail();

        return view('genus.show', compact('genusData'));
    }
}
