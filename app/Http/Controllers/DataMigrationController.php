<?php

namespace App\Http\Controllers;

use App\Models\AkaUser;
use App\Models\KupnesUser;
use App\Models\User;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    public function migrateData()
    {
        // Ambil data dari database aka
        $akaUsers = AkaUser::all();

        // Ambil data dari database kupnes
        $kupnesUsers = KupnesUser::all();

        // Simpan data ke database utama
        foreach ($akaUsers as $akaUser) {
            User::create([
                'name' => $akaUser->uname,
                'email' => $akaUser->email,
                'password' => $akaUser->password,
                // Tambahkan field lain sesuai kebutuhan
            ]);
        }

        foreach ($kupnesUsers as $kupnesUser) {
            User::create([
                'name' => $kupnesUser->uname,
                'email' => $kupnesUser->email,
                'password' => $kupnesUser->password,
                // Tambahkan field lain sesuai kebutuhan
            ]);
        }

        return response()->json(['message' => 'Data migrated successfully']);
    }
}
