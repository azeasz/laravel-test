<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGenusFaunasSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua data dari tabel genus_faunas
        $genusFaunas = DB::table('genus_faunas')->get();

        foreach ($genusFaunas as $genusFauna) {
            // Cari fauna_id yang sesuai dari tabel faunas berdasarkan genus
            $fauna = DB::table('faunas')->where('nameLat', $genusFauna->genus)->first();

            if ($fauna) {
                // Update fauna_id di tabel genus_faunas
                DB::table('genus_faunas')
                    ->where('id', $genusFauna->id)
                    ->update(['fauna_id' => $fauna->id]);
            }
        }
    }
}
