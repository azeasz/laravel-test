<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ExternalApiService
{
    protected $burungnesiaUrl = 'https://burungnesia.org/api/checklist';
    protected $kupunesiaUrl = 'https://kupunesia.org/api/checklist';

    public function getBurungnesia()
    {
        try {
            return Cache::remember('burungnesia_data', 3600, function () {
                $response = Http::get($this->burungnesiaUrl);
                if ($response->successful()) {
                    $data = $response->json();
                    return array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'latitude' => $item['latitude'],
                            'longitude' => $item['longitude'],
                            'observer_name' => $item['observer'],
                            'location' => $item['lokasi'],
                            'habitat' => $item['habitat'],
                            'observation_date' => $item['tgl_pengamatan'],
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                            'created_at' => $item['createdAt'],
                            'updated_at' => $item['updatedAt'],
                            'checklist' => array_map(function($species) {
                                return [
                                    'nameLat' => $species['nameLat'],
                                    'family' => $species['family'],
                                    'nameEn' => $species['nameEn'],
                                    'count' => $species['count'],
                                    'notes' => $species['notes'],
                                    'breeding' => $species['breeding'] ?? false,
                                    'breedingNote' => $species['breedingNote'] ?? '',
                                ];
                            }, $item['checklist'])
                        ];
                    }, $data['data']);
                }
                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Burungnesia API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getKupunesia()
    {
        try {
            return Cache::remember('kupunesia_data', 3600, function () {
                $response = Http::get($this->kupunesiaUrl);
                if ($response->successful()) {
                    $data = $response->json();
                    return array_map(function($item) {
                        return [
                            'id' => $item['id'],
                            'latitude' => $item['latitude'],
                            'longitude' => $item['longitude'],
                            'observer_name' => $item['observer'],
                            'location' => $item['lokasi'],
                            'habitat' => $item['habitat'],
                            'observation_date' => $item['tgl_pengamatan'],
                            'created_at' => $item['createdAt'],
                            'updated_at' => $item['updatedAt'],
                            'checklist' => array_map(function($species) {
                                return [
                                    'nameLat' => $species['nameLat'],
                                    'family' => $species['family'],
                                    'count' => $species['count'],
                                    'notes' => $species['notes'] ?? '',
                                ];
                            }, $item['checklist'])
                        ];
                    }, $data['data']);
                }
                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Kupunesia API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getRecentActivities($limit = 10)
    {
        $activities = collect();

        // Ambil data Burungnesia
        $burungnesia = $this->getBurungnesia() ?? [];
        foreach ($burungnesia as $item) {
            foreach ($item['checklist'] as $checklist) {
                $activities->push([
                    'type' => 'burungnesia',
                    'id' => $item['id'],
                    'title' => 'Observasi Burungnesia',
                    'description' => $checklist['nameLat'],
                    'user' => $item['observer_name'],
                    'date' => Carbon::parse($item['created_at']),
                    'icon' => 'fa-dove',
                    'color' => 'text-warning',
                    'source' => 'Burungnesia',
                    'detail_url' => route('admin.burungnesia.show', $item['id'])
                ]);
            }
        }

        // Ambil data Kupunesia
        $kupunesia = $this->getKupunesia() ?? [];
        foreach ($kupunesia as $item) {
            foreach ($item['checklist'] as $checklist) {
                $activities->push([
                    'type' => 'kupunesia',
                    'id' => $item['id'],
                    'title' => 'Observasi Kupunesia',
                    'description' => $checklist['nameLat'],
                    'user' => $item['observer_name'],
                    'date' => Carbon::parse($item['created_at']),
                    'icon' => 'fa-butterfly',
                    'color' => 'text-info',
                    'source' => 'Kupunesia',
                    'detail_url' => route('admin.kupunesia.show', $item['id'])
                ]);
            }
        }

        return $activities->sortByDesc('date')->take($limit);
    }

    public function getMapLocations()
    {
        $locations = collect();

        // Lokasi dari Burungnesia
        $burungnesia = $this->getBurungnesia() ?? [];
        foreach ($burungnesia as $item) {
            if (isset($item['latitude']) && isset($item['longitude'])) {
                $locations->push([
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'name' => $item['checklist'][0]['nameLat'] ?? 'Unknown Species',
                    'details' => $item['location'],
                    'count' => count($item['checklist']),
                    'source' => 'Burungnesia'
                ]);
            }
        }

        // Lokasi dari Kupunesia
        $kupunesia = $this->getKupunesia() ?? [];
        foreach ($kupunesia as $item) {
            if (isset($item['latitude']) && isset($item['longitude'])) {
                $locations->push([
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'name' => $item['checklist'][0]['nameLat'] ?? 'Unknown Species',
                    'details' => $item['location'],
                    'count' => count($item['checklist']),
                    'source' => 'Kupunesia'
                ]);
            }
        }

        return $locations;
    }
}
