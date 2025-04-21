<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GridSystemService;

class GenerateGridFromGeoJson extends Command
{
    protected $signature = 'grid:from-geojson';
    protected $description = 'Generate grid cells from GeoJSON data';

    public function handle()
    {
        $this->info('Reading GeoJSON file...');

        try {
            $geojsonPath = storage_path('app/public/geo/indonesia-prov.geojson');

            if (!file_exists($geojsonPath)) {
                throw new \Exception('GeoJSON file not found at: ' . $geojsonPath);
            }

            $geojsonContent = file_get_contents($geojsonPath);
            $geojson = json_decode($geojsonContent, true);

            if (!$geojson || !isset($geojson['features'])) {
                throw new \Exception('Invalid GeoJSON format');
            }

            $features = $geojson['features'];
            $this->info('Processing ' . count($features) . ' provinces...');
            $bar = $this->output->createProgressBar(count($features));

            foreach ($features as $feature) {
                try {
                    if (!isset($feature['geometry']['coordinates'])) {
                        continue;
                    }

                    $properties = $feature['properties'];
                    $geometry = $feature['geometry'];
                    $bounds = $this->calculateBounds($geometry['coordinates']);

                    app(GridSystemService::class)->generateGridForBounds(
                        $bounds,
                        [
                            'name' => $properties['Propinsi'] ?? 'Unknown',
                            'type' => 'provinsi',
                            'area_id' => $properties['ID'] ?? null
                        ]
                    );

                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\nError processing province {$properties['Propinsi']}: " . $e->getMessage());
                    continue;
                }
            }

            $bar->finish();
            $this->info("\nGrid generation completed!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }

    private function calculateBounds($coordinates)
    {
        $lats = [];
        $lngs = [];

        if (!is_array($coordinates)) {
            throw new \Exception('Invalid coordinates format');
        }

        // Handle MultiPolygon dan Polygon
        if ($this->isMultiPolygon($coordinates)) {
            foreach ($coordinates as $polygon) {
                $this->processPolygonCoordinates($polygon[0], $lats, $lngs);
            }
        } else {
            // Single polygon
            $this->processPolygonCoordinates($coordinates[0], $lats, $lngs);
        }

        if (empty($lats) || empty($lngs)) {
            throw new \Exception('No valid coordinates found');
        }

        return [
            'min_lat' => min($lats),
            'max_lat' => max($lats),
            'min_lng' => min($lngs),
            'max_lng' => max($lngs)
        ];
    }

    private function isMultiPolygon($coordinates)
    {
        return isset($coordinates[0][0][0]) && is_array($coordinates[0][0][0]);
    }

    private function processPolygonCoordinates($coordinates, &$lats, &$lngs)
    {
        foreach ($coordinates as $coord) {
            if (!is_array($coord) || count($coord) < 2) {
                continue;
            }

            $lng = (float)$coord[0];
            $lat = (float)$coord[1];

            // Validasi koordinat
            if ($this->isValidCoordinate($lat, $lng)) {
                $lats[] = $lat;
                $lngs[] = $lng;
            }
        }
    }

    private function isValidCoordinate($lat, $lng)
    {
        return $lat >= -90 &&
               $lat <= 90 &&
               $lng >= -180 &&
               $lng <= 180 &&
               !is_null($lat) &&
               !is_null($lng) &&
               is_numeric($lat) &&
               is_numeric($lng);
    }
}
