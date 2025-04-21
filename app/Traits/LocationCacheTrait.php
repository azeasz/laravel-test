<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

trait LocationCacheTrait
{
    protected function getCachedLocationDetails($latitude, $longitude)
    {
        $cacheKey = "location_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($latitude, $longitude) {
            return $this->fetchLocationDetails($latitude, $longitude);
        });
    }

    protected function fetchLocationDetails($latitude, $longitude)
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";

            $response = Http::withHeaders([
                'User-Agent' => 'FOBi Application'
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['address'])) {
                    return [
                        'city' => $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? null,
                        'regency' => $data['address']['county'] ?? $data['address']['city_district'] ?? null,
                        'province' => $data['address']['state'] ?? null,
                        'country' => $data['address']['country'] ?? null,
                        'bounds' => [
                            'north' => $data['boundingbox'][1] ?? null,
                            'south' => $data['boundingbox'][0] ?? null,
                            'east' => $data['boundingbox'][3] ?? null,
                            'west' => $data['boundingbox'][2] ?? null
                        ],
                        'updated_at' => now()
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching location details:', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }
    }
}
