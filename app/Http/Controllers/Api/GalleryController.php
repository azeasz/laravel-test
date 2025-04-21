<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        // Contoh data galeri foto yang akan dikembalikan
        $gallery = [
            [
                'title' => 'Biru-laut ekor-blorok',
                'image' => asset('storage/icon/blt.jpeg'),
                'description' => 'Bar-Tailed Godwit<br><i>Limosa lapponica</i><br><i>Limosa</i><br><i>Scolopacidae</i>',
                'contributor' => 'Sikebo',
                'category' => 'Burungnesia',
                'count' => 16
            ],
            [
                'title' => 'Bentet',
                'image' => asset('storage/icon/btt.jpg'),
                'description' => 'Shrike<br><i>Lanius sp</i><br><i>Laniidae</i>',
                'contributor' => 'Sikebo',
                'category' => 'Burungnesia',
                'count' => 13
            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ];

        return response()->json($gallery);
    }
}
