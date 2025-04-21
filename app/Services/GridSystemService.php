<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GridSystemService
{
    // Ukuran grid dalam derajat
    const GRID_SIZES = [
        'pulau' => 1.0,      // ~110km
        'provinsi' => 0.5,    // ~55km
        'kabupaten' => 0.25,  // ~27.5km
        'kota' => 0.1        // ~11km
    ];

    public function generateGridForBounds($bounds, $areaInfo)
    {
        $gridSize = self::GRID_SIZES[$areaInfo['type']] ?? self::GRID_SIZES['kota'];

        // Pastikan semua nilai adalah float
        $minLat = (float)floor($bounds['min_lat'] / $gridSize) * $gridSize;
        $maxLat = (float)ceil($bounds['max_lat'] / $gridSize) * $gridSize;
        $minLng = (float)floor($bounds['min_lng'] / $gridSize) * $gridSize;
        $maxLng = (float)ceil($bounds['max_lng'] / $gridSize) * $gridSize;

        $grids = [];

        for ($lat = $minLat; $lat < $maxLat; $lat += $gridSize) {
            for ($lng = $minLng; $lng < $maxLng; $lng += $gridSize) {
                $cellX = (int)floor($lng / $gridSize);
                $cellY = (int)floor($lat / $gridSize);

                // Cek apakah grid cell sudah ada
                $exists = DB::table('grid_cells')
                    ->where('cell_x', $cellX)
                    ->where('cell_y', $cellY)
                    ->where('area_type', $areaInfo['type'])
                    ->exists();

                if (!$exists) {
                    $grids[] = [
                        'cell_x' => $cellX,
                        'cell_y' => $cellY,
                        'min_lat' => (float)$lat,
                        'max_lat' => (float)($lat + $gridSize),
                        'min_lng' => (float)$lng,
                        'max_lng' => (float)($lng + $gridSize),
                        'area_type' => $areaInfo['type'],
                        'area_name' => $areaInfo['name'],
                        'area_id' => $areaInfo['area_id']
                    ];

                    if (count($grids) >= 1000) {
                        DB::table('grid_cells')->insert($grids);
                        $grids = [];
                    }
                }
            }
        }

        if (!empty($grids)) {
            DB::table('grid_cells')->insert($grids);
        }
    }

    public function findGridCell($lat, $lng, $areaType = 'kota')
    {
        $gridSize = self::GRID_SIZES[$areaType];

        return DB::table('grid_cells')
            ->where('cell_x', floor($lng / $gridSize))
            ->where('cell_y', floor($lat / $gridSize))
            ->where('area_type', $areaType)
            ->first();
    }
}
