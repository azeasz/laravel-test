<?php

namespace Database\Seeders;

use App\Models\Taxon;
use App\Models\FobiUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxonomySeeder extends Seeder
{
    public function run()
    {
        $adminUser = FobiUser::first();
        if (!$adminUser) {
            $this->command->error('No admin user found! Please create a user first.');
            return;
        }

        $basePath = storage_path('app/public/add/csv_output');
        
        if (!File::exists($basePath)) {
            $this->command->error("Directory not found: {$basePath}");
            return;
        }
        
        // Ubah untuk membaca file langsung dari direktori
        $files = File::files($basePath);
        
        $this->command->info("Found " . count($files) . " files");
        
        if (empty($files)) {
            $this->command->error("No files found in: {$basePath}");
            return;
        }

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                try {
                    DB::beginTransaction();

                    $this->command->info("Importing: " . $file->getFilename());
                    $this->importCsvFile($file, $adminUser->id);

                    DB::commit();
                    $this->command->info("Successfully imported: " . $file->getFilename());
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error importing file {$file->getFilename()}: " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    $this->command->error("Failed importing: " . $file->getFilename() . " - " . $e->getMessage());
                }
            }
        }
    }

    private function importCsvFile($file, $userId)
    {
        $header = null;
        $data = array();
        $rowNumber = 0;

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                try {
                    if (!$header) {
                        $header = array_map('trim', $row);
                    } else {
                        if (count($row) === count($header)) {
                            $combinedRow = array_combine($header, array_map('trim', $row));
                            if ($combinedRow) {
                                $this->createTaxonRecord($combinedRow, $userId);
                            }
                        } else {
                            Log::warning("Row {$rowNumber} in {$file->getFilename()} has incorrect number of columns");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing row {$rowNumber} in {$file->getFilename()}: " . $e->getMessage());
                    throw $e;
                }
            }
            fclose($handle);
        }
    }

    private function createTaxonRecord($row, $userId)
    {
        // Tentukan apakah ini record Plantae atau Animalia
        $isPlantae = strtolower($row['kingdom'] ?? '') === 'plantae';

        return Taxon::create([
            'taxon_key' => $this->parseNumeric($row['taxonKey'] ?? null),
            'scientific_name' => $row['scientificName'] ?? null,
            'accepted_taxon_key' => $this->parseNumeric($row['acceptedTaxonKey'] ?? null),
            'accepted_scientific_name' => $row['acceptedScientificName'] ?? null,
            'taxon_rank' => $row['taxonRank'] ?? null,
            'taxonomic_status' => $row['taxonomicStatus'] ?? null,
            'domain' => $row['domain'] ?? null,
            'cname_domain' => $row['cnamedomain'] ?? null,
            'superkingdom' => $row['superkingdom'] ?? null,
            'cname_superkingdom' => $row['cnamesuperkingdom'] ?? null,
            'kingdom' => $row['kingdom'] ?? null,
            'cname_kingdom' => $row['cnameKingdom'] ?? null,
            'kingdom_key' => $this->parseNumeric($row['kingdomKey'] ?? null),
            'subkingdom' => $row['subkingdom'] ?? null,
            'cname_subkingdom' => $row['cnamesubkingdom'] ?? null,

            // Conditional fields berdasarkan kingdom
            'superphylum' => $isPlantae ? null : ($row['superphylum'] ?? null),
            'cname_superphylum' => $isPlantae ? null : ($row['cnamesuperphylum'] ?? null),
            'phylum' => $isPlantae ? null : ($row['phylum'] ?? null),
            'cname_phylum' => $isPlantae ? null : ($row['cnamePhylum'] ?? null),
            'phylum_key' => $isPlantae ? null : $this->parseNumeric($row['phylumKey'] ?? null),
            'subphylum' => $isPlantae ? null : ($row['subphylum'] ?? null),
            'cname_subphylum' => $isPlantae ? null : ($row['cnamesubphylum'] ?? null),

            // Fields khusus untuk Plantae
            'superdivision' => $isPlantae ? ($row['superdivision'] ?? null) : null,
            'cname_superdivision' => $isPlantae ? ($row['cnamesuperdivision'] ?? null) : null,
            'division' => $isPlantae ? ($row['division'] ?? null) : null,
            'cname_division' => $isPlantae ? ($row['cnamedivision'] ?? null) : null,
            'division_key' => $isPlantae ? $this->parseNumeric($row['divisionKey'] ?? null) : null,
            'subdivision' => $isPlantae ? ($row['subdivision'] ?? null) : null,
            'cname_subdivision' => $isPlantae ? ($row['cnamesubdivision'] ?? null) : null,

            // Lanjutan fields yang sama untuk kedua kingdom
            'superclass' => $row['superclass'] ?? null,
            'cname_superclass' => $row['cnamesuperclass'] ?? null,
            'class' => $row['class'] ?? null,
            'cname_class' => $row['cnameClass'] ?? null,
            'class_key' => $this->parseNumeric($row['classKey'] ?? null),
            'subclass' => $row['subclass'] ?? null,
            'cname_subclass' => $row['cnamesubclass'] ?? null,
            'infraclass' => $row['infraclass'] ?? null,
            'cname_infraclass' => $row['cnameinfraclass'] ?? null,
            'subterclass' => $row['subterclass'] ?? null,
            'superorder' => $row['superorder'] ?? null,
            'cname_superorder' => $row['cnamesuperorder'] ?? null,
            'order' => $row['order'] ?? null,
            'cname_order' => $row['cnameOrder'] ?? null,
            'order_key' => $this->parseNumeric($row['orderKey'] ?? null),
            'suborder' => $row['suborder'] ?? null,
            'cname_suborder' => $row['cnamesuborder'] ?? null,
            'infraorder' => $row['infraorder'] ?? null,
            'superfamily' => $row['superfamily'] ?? null,
            'cname_superfamily' => $row['cnamesuperfamily'] ?? null,
            'family' => $row['family'] ?? null,
            'cname_family' => $row['cnameFamily'] ?? null,
            'family_key' => $this->parseNumeric($row['familyKey'] ?? null),
            'subfamily' => $row['subfamily'] ?? null,
            'cname_subfamily' => $row['cnamesubfamily'] ?? null,
            'supertribe' => $row['supertribe'] ?? null,
            'cname_supertribe' => $row['cnamesupertribe'] ?? null,
            'tribe' => $row['tribe'] ?? null,
            'cname_tribe' => $row['cnametribe'] ?? null,
            'subtribe' => $row['subtribe'] ?? null,
            'cname_subtribe' => $row['cnamesubtribe'] ?? null,
            'genus' => $row['genus'] ?? null,
            'cname_genus' => $row['cnameGenus'] ?? null,
            'genus_key' => $this->parseNumeric($row['genusKey'] ?? null),
            'subgenus' => $row['subgenus'] ?? null,
            'cname_subgenus' => $row['cnamesubgenus'] ?? null,
            'species' => $row['species'] ?? null,
            'cname_species' => $row['cnameSpecies'] ?? null,
            'species_key' => $this->parseNumeric($row['speciesKey'] ?? null),
            'subspecies' => $row['subspecies'] ?? null,
            'cname_subspecies' => $row['cnamesubspecies'] ?? null,
            'variety' => $row['variety'] ?? null,
            'cname_variety' => $row['cnamevariety'] ?? null,
            'iucn_red_list_category' => $row['iucnRedListCategory'] ?? null,
            'status_kepunahan' => $row['statuskepunahan'] ?? null,
            'status' => 'active',
            'created_by' => $userId,
            'updated_by' => $userId
        ]);
    }

    private function parseNumeric($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }
        return $value;
    }
}
