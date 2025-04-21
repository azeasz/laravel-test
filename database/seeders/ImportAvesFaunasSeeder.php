<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportAvesFaunasSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Baca file CSV
            $csv = Reader::createFromPath(storage_path('app/burnes/AVES.csv'), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $total = 0;
            $inserted = 0;

            foreach ($records as $record) {
                $total++;

                // Skip jika scientific name kosong
                if (empty($record['scientificName'])) {
                    continue;
                }

                // Cek apakah data sudah ada
                $exists = DB::table('faunas')
                    ->where('nameLat', $record['scientificName'])
                    ->exists();

                if (!$exists) {
                    // Insert ke tabel faunas
                    DB::table('faunas')->insert([
                        'nameLat' => $record['scientificName'],
                        'nameId' => $record['cnameSpecies'] ?? '-',
                        'nameEn' => '', // Kosong karena tidak ada di CSV
                        'family' => $record['family'] ?? '',
                        'keyword' => '',
                        'source' => 'AVES.csv',
                        'is_protection' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $inserted++;
                }

                if ($total % 100 === 0) {
                    $this->command->info("Processed $total records...");
                }
            }

            DB::commit();
            $this->command->info("Import selesai: $inserted dari $total data berhasil diimport");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
