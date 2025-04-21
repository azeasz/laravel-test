<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GridCellController extends Controller
{
    public function findCells(Request $request)
    {
        try {
            $lat = $request->input('latitude');
            $lng = $request->input('longitude');
            $name = $request->input('name');
            $type = $request->input('type', 'point');

            // Buat POINT dari koordinat yang diberikan
            $point = "POINT($lng $lat)";

            if ($type === 'province') {
                // Gunakan grid_cells untuk level provinsi
                return $this->findByProvince($name, $point);
            } else {
                // Gunakan spatial query untuk level lainnya
                return $this->findByLocation($point, $name, $type);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function findByProvince($name, $point)
    {
        $results = DB::table('grid_cells')
            ->where('area_type', 'provinsi')
            ->where('area_name', 'LIKE', "%$name%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        // Gabungkan dengan data checklist
        $boundingBox = [
            'min_lat' => $results->min('min_lat'),
            'max_lat' => $results->max('max_lat'),
            'min_lng' => $results->min('min_lng'),
            'max_lng' => $results->max('max_lng')
        ];

        return $this->findChecklistsInBounds($boundingBox, 'province', $name);
    }

    private function findByLocation($point, $name, $type)
    {
        // Query untuk mencari checklist dalam radius tertentu
        $radius = $this->getRadiusByType($type);

        // Gunakan ST_Distance_Sphere untuk mencari dalam radius
        $checklists = DB::select("
            (SELECT
                latitude, longitude, id,
                'fobi' as source,
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    ST_GeomFromText(?),
                    6371000
                ) as distance
            FROM fobi_checklists
            HAVING distance <= ?)
            UNION ALL
            (SELECT
                latitude, longitude, id,
                'fobi_kupnes' as source,
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    ST_GeomFromText(?),
                    6371000
                ) as distance
            FROM fobi_checklists_kupnes
            HAVING distance <= ?)
            UNION ALL
            (SELECT
                latitude, longitude, id,
                'burungnesia' as source,
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    ST_GeomFromText(?),
                    6371000
                ) as distance
            FROM second.checklists
            HAVING distance <= ?)
            UNION ALL
            (SELECT
                latitude, longitude, id,
                'kupunesia' as source,
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    ST_GeomFromText(?),
                    6371000
                ) as distance
            FROM third.checklists
            HAVING distance <= ?)
            ORDER BY distance
        ", [$point, $radius, $point, $radius, $point, $radius, $point, $radius]);

        // Hitung bounding box dari hasil
        $boundingBox = $this->calculateBoundingBox($checklists);

        return response()->json([
            'checklists' => $checklists,
            'bounding_box' => $boundingBox,
            'type' => $type,
            'name' => $name
        ]);
    }

    private function getRadiusByType($type)
    {
        return match($type) {
            'city' => 10000,     // 10km untuk kota
            'district' => 5000,   // 5km untuk kecamatan
            'village' => 2000,    // 2km untuk desa
            default => 1000       // 1km untuk point
        };
    }

    private function findChecklistsInBounds($bounds, $type, $name)
    {
        $checklists = DB::select("
            SELECT * FROM (
                (SELECT latitude, longitude, id, 'fobi' as source
                FROM fobi_checklists
                WHERE latitude BETWEEN ? AND ?
                AND longitude BETWEEN ? AND ?)
                UNION ALL
                (SELECT latitude, longitude, id, 'fobi_kupnes' as source
                FROM fobi_checklists_kupnes
                WHERE latitude BETWEEN ? AND ?
                AND longitude BETWEEN ? AND ?)
                UNION ALL
                (SELECT latitude, longitude, id, 'burungnesia' as source
                FROM second.checklists
                WHERE latitude BETWEEN ? AND ?
                AND longitude BETWEEN ? AND ?)
                UNION ALL
                (SELECT latitude, longitude, id, 'kupunesia' as source
                FROM third.checklists
                WHERE latitude BETWEEN ? AND ?
                AND longitude BETWEEN ? AND ?)
            ) as combined_results
        ", [
            $bounds['min_lat'], $bounds['max_lat'], $bounds['min_lng'], $bounds['max_lng'],
            $bounds['min_lat'], $bounds['max_lat'], $bounds['min_lng'], $bounds['max_lng'],
            $bounds['min_lat'], $bounds['max_lat'], $bounds['min_lng'], $bounds['max_lng'],
            $bounds['min_lat'], $bounds['max_lat'], $bounds['min_lng'], $bounds['max_lng']
        ]);

        return response()->json([
            'checklists' => $checklists,
            'bounding_box' => $bounds,
            'type' => $type,
            'name' => $name
        ]);
    }

    private function calculateBoundingBox($checklists)
    {
        if (empty($checklists)) {
            return null;
        }

        $lats = array_column($checklists, 'latitude');
        $lngs = array_column($checklists, 'longitude');

        return [
            'min_lat' => min($lats),
            'max_lat' => max($lats),
            'min_lng' => min($lngs),
            'max_lng' => max($lngs),
            'center_lat' => (min($lats) + max($lats)) / 2,
            'center_lng' => (min($lngs) + max($lngs)) / 2
        ];
    }
}
