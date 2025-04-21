<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Taxontest;
use League\Csv\Reader;

class TaxontestSeeder extends Seeder
{
    public function run()
    {
        $csv = Reader::createFromPath(storage_path('app/public/taxontest.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Taxontest::create([
                'kingdom' => $record['kingdom'],
                'kingdomKey' => $record['kingdomKey'],
                'cnameClass' => $record['cnameClass'],
                'family' => $record['family'],
                'cnameFamily' => $record['cnameFamily'],
                'genus' => $record['genus'],
                'species' => $record['species'],
                'cnameSpecies' => $record['cnameSpecies'],
                'taxonRank' => $record['taxonRank'],
            ]);
        }
    }
}
