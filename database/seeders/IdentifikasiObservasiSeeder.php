<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IdentifikasiObservasi;

class IdentifikasiObservasiSeeder extends Seeder
{
    public function run()
    {
        IdentifikasiObservasi::factory()->count(10)->create();
    }
}
