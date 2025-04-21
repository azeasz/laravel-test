<?php

namespace Database\Seeders;

use App\Models\FobiUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class TaxaSeeder extends Seeder
{
    public function run()
    {
        // Cek admin user
        $adminUser = FobiUser::first();
        if (!$adminUser) {
            $this->command->error('No admin user found! Please create a user first.');
            return;
        }

        $basePath = storage_path('app/burnes');
        $files = File::files($basePath);

        foreach ($files as $file) {
            if (str_contains($file->getFilename(), 'AVES')) {
                try {
                    DB::beginTransaction();

                    $this->command->info("Importing: " . $file->getFilename());

                    // Baca file CSV
                    $handle = fopen($file->getPathname(), 'r');
                    $headers = fgetcsv($handle);
                    $imported = 0;
                    $skipped = 0;

                    while (($data = fgetcsv($handle)) !== false) {
                        $row = array_combine($headers, $data);

                        // Skip jika nama spesies kosong
                        if (empty($row['scientificName'])) {
                            $skipped++;
                            continue;
                        }

                        // Data untuk insert
                        $taxaData = [
                            'scientific_name' => $row['scientificName'],
                            'taxon_rank' => $row['taxonRank'],
                            'kingdom' => $row['kingdom'],
                            'cname_kingdom' => $row['cnameKindom'],
                            'phylum' => $row['phylum'],
                            'cname_phylum' => $row['cnamePhylum'],
                            'class' => $row['class'],
                            'cname_class' => $row['cnameClass'],
                            'order' => $row['order'],
                            'cname_order' => $row['cnameOrder'],
                            'family' => $row['family'],
                            'cname_family' => $row['cnameFamily'],
                            'genus' => $row['genus'],
                            'cname_genus' => $row['cnameGenus'],
                            'species' => $row['scientificName'],
                            'cname_species' => $row['cnameSpecies'],
                            'status' => 'active',
                            'created_by' => $adminUser->id,
                            'updated_by' => $adminUser->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        // Insert ke database
                        DB::table('taxas')->insert($taxaData);
                        $imported++;

                        if ($imported % 100 === 0) {
                            $this->command->info("Processed $imported records...");
                        }
                    }

                    fclose($handle);
                    DB::commit();

                    $this->command->info("Import selesai: $imported data berhasil diimpor, $skipped data dilewati");

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->command->error("Error: " . $e->getMessage());
                }
            }
        }
    }
}
