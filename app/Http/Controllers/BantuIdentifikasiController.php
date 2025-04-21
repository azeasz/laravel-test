<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BantuIdentifikasiController extends Controller
{
    public function index()
    {
        // Data dummy untuk observasi
        $observations = [
            ['id' => 1, 'name' => 'Observasi 1', 'status' => 'BANTU IDEN'],
            ['id' => 2, 'name' => 'Observasi 2', 'status' => 'ID KURANG'],
            // Tambahkan data dummy lainnya sesuai kebutuhan
        ];

        return view('bantu_identifikasi.index', compact('observations'));
    }
}
