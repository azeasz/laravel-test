<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class TaxaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $csv = Reader::createFromPath(storage_path('app/public/phy-annelida-sort.csv'), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        foreach ($records as $record) {
            // Debugging: Print the record to check the data
            print_r($record);

            DB::table('taxa')->insert([
                'taxonKey' => $record['taxonKey'] ?? null,
                'scientificName' => $record['scientificName'] ?? null,
                'acceptedTaxonKey' => $record['acceptedTaxonKey'] ?? null,
                'acceptedScientificName' => $record['acceptedScientificName'] ?? null,
                'taxonRank' => $record['taxonRank'] ?? null,
                'taxonomicStatus' => $record['taxonomicStatus'] ?? null,
                'kingdom' => $record['kingdom'] ?? null,
                'cnameKingdom' => $record['cnameKingdom'] ?? null,
                'kingdomKey' => $record['kingdomKey'] ?? null,
                'phylum' => $record['phylum'] ?? null,
                'cnamePhylum' => $record['cnamePhylum'] ?? null,
                'phylumKey' => $record['phylumKey'] ?? null,
                'class' => $record['class'] ?? null,
                'cnameClass' => $record['cnameClass'] ?? null,
                'classKey' => $record['classKey'] ?? null,
                'order' => $record['order'] ?? null,
                'cnameOrder' => $record['cnameOrder'] ?? null,
                'orderKey' => $record['orderKey'] ?? null,
                'kategotiumum' => $record['kategotiumum'] ?? null,
                'family' => $record['family'] ?? null,
                'cnameFamily' => $record['cnameFamily'] ?? null,
                'familyKey' => $record['familyKey'] ?? null,
                'genus' => $record['genus'] ?? null,
                'cnameGenus' => $record['cnameGenus'] ?? null,
                'genusKey' => $record['genusKey'] ?? null,
                'species' => $record['species'] ?? null,
                'cnameSpecies' => $record['cnameSpecies'] ?? null,
                'speciesKey' => $record['speciesKey'] ?? null,
                'iucnRedListCategory' => $record['iucnRedListCategory'] ?? null,
                'subspecies' => $record['subspecies'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
