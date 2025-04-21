<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GetObservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UnifiedObservationController extends Controller
{
    public function getObservations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 50);
            $cacheKey = $this->generateCacheKey($request);

            // Coba ambil dari cache dulu
            return Cache::remember($cacheKey, now()->addMinutes(5), function() use ($request, $perPage) {
                // Query untuk FOBI
                $fobiData = $this->getFobiData($request);

                // Query untuk Burungnesia
                $burungnesiadata = $this->getBurungnesiaData($request);

                // Query untuk Kupunesia
                $kupunesiaData = $this->getKupunesiaData($request);

                // Gabungkan semua data
                $allData = collect()
                    ->concat($fobiData)
                    ->concat($burungnesiadata)
                    ->concat($kupunesiaData)
                    ->sortByDesc('created_at');

                // Manual pagination
                $page = $request->input('page', 1);
                $total = $allData->count();
                $items = $allData->forPage($page, $perPage);

                // Proses media dan count untuk setiap item
                $items = $this->processItems($items);

                return response()->json([
                    'success' => true,
                    'data' => $items->values()->all(),
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $total,
                        'last_page' => ceil($total / $perPage)
                    ]
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in getObservations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data observasi'
            ], 500);
        }
    }

    private function getFobiData($request)
    {
        return DB::table('fobi_checklist_taxas')
            ->join('taxa_quality_assessments', 'fobi_checklist_taxas.id', '=', 'taxa_quality_assessments.taxa_id')
            ->join('fobi_users', 'fobi_checklist_taxas.user_id', '=', 'fobi_users.id')
            ->join('taxas', 'fobi_checklist_taxas.taxa_id', '=', 'taxas.id')
            ->select($this->getFobiFields())
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->search($request->search);
            })
            ->when($request->filled(['latitude', 'longitude', 'radius']), function($q) use ($request) {
                return $q->filterByLocation(
                    $request->latitude,
                    $request->longitude,
                    $request->radius
                );
            })
            ->filterByDate($request->start_date, $request->end_date)
            ->get();
    }

    private function getBurungnesiaData($request)
    {
        return DB::table('fobi_checklists')
            ->join('fobi_checklist_faunasv1', 'fobi_checklists.id', '=', 'fobi_checklist_faunasv1.checklist_id')
            ->join('data_quality_assessments', function($join) {
                $join->on('fobi_checklists.id', '=', 'data_quality_assessments.observation_id')
                     ->on('fobi_checklist_faunasv1.fauna_id', '=', 'data_quality_assessments.fauna_id');
            })
            ->join('fobi_users', 'fobi_checklists.fobi_user_id', '=', 'fobi_users.id')
            ->join(DB::connection('second')->getDatabaseName().'.faunas',
                'fobi_checklist_faunasv1.fauna_id', '=', 'faunas.id')
            ->select([
                'fobi_checklists.id',
                'fobi_checklist_faunasv1.fauna_id',
                'fobi_checklists.fobi_user_id as user_id',
                'faunas.nameLat as scientific_name',
                'fobi_checklists.latitude',
                'fobi_checklists.longitude',
                'fobi_checklists.created_at',
                'fobi_checklists.updated_at',
                'data_quality_assessments.grade',
                'data_quality_assessments.has_media',
                'data_quality_assessments.is_wild',
                'data_quality_assessments.location_accurate',
                'data_quality_assessments.needs_id',
                'data_quality_assessments.community_id_level',
                'fobi_users.uname as observer_name',
                'faunas.nameId as cname_species',
                'faunas.nameLat as taxa_scientific_name',
                DB::raw("'burungnesia' as source")
            ])
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->search($request->search);
            })
            ->when($request->filled(['latitude', 'longitude', 'radius']), function($q) use ($request) {
                return $q->filterByLocation(
                    $request->latitude,
                    $request->longitude,
                    $request->radius
                );
            })
            ->filterByDate($request->start_date, $request->end_date)
            ->get();
    }

    private function getKupunesiaData($request)
    {
        return DB::table('fobi_checklists_kupnes')
            ->join('fobi_checklist_faunasv2', 'fobi_checklists_kupnes.id', '=', 'fobi_checklist_faunasv2.checklist_id')
            ->join('data_quality_assessments_kupnes', function($join) {
                $join->on('fobi_checklists_kupnes.id', '=', 'data_quality_assessments_kupnes.observation_id')
                     ->on('fobi_checklist_faunasv2.fauna_id', '=', 'data_quality_assessments_kupnes.fauna_id');
            })
            ->join('fobi_users', 'fobi_checklists_kupnes.fobi_user_id', '=', 'fobi_users.id')
            ->join(DB::connection('third')->getDatabaseName().'.faunas',
                'fobi_checklist_faunasv2.fauna_id', '=', 'faunas.id')
            ->select([
                'fobi_checklists_kupnes.id',
                'fobi_checklist_faunasv2.fauna_id',
                'fobi_checklists_kupnes.fobi_user_id as user_id',
                'faunas.nameLat as scientific_name',
                'fobi_checklists_kupnes.latitude',
                'fobi_checklists_kupnes.longitude',
                'fobi_checklists_kupnes.created_at',
                'fobi_checklists_kupnes.updated_at',
                'data_quality_assessments_kupnes.grade',
                'data_quality_assessments_kupnes.has_media',
                'data_quality_assessments_kupnes.is_wild',
                'data_quality_assessments_kupnes.location_accurate',
                'data_quality_assessments_kupnes.needs_id',
                'data_quality_assessments_kupnes.community_id_level',
                'fobi_users.uname as observer_name',
                'faunas.nameId as cname_species',
                'faunas.nameLat as taxa_scientific_name',
                DB::raw("'kupunesia' as source")
            ])
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->search($request->search);
            })
            ->when($request->filled(['latitude', 'longitude', 'radius']), function($q) use ($request) {
                return $q->filterByLocation(
                    $request->latitude,
                    $request->longitude,
                    $request->radius
                );
            })
            ->filterByDate($request->start_date, $request->end_date)
            ->get();
    }

    private function processItems($items)
    {
        return $items->map(function($item) {
            // Tambahkan media
            $item->images = $this->getItemImages($item);
            $item->audio = $this->getItemAudio($item);

            // Hitung total observasi
            $item->fobi_count = $this->getFobiCount($item);
            $item->burungnesia_count = $this->getBurungnesiaCount($item);
            $item->kupunesia_count = $this->getKupunesiaCount($item);

            return $item;
        });
    }

    private function generateCacheKey(Request $request)
    {
        return 'observations.' . md5(json_encode($request->all()));
    }

    private function getFobiFields()
    {
        return [
            'fobi_checklist_taxas.id',
            'fobi_checklist_taxas.taxa_id',
            'fobi_checklist_taxas.user_id',
            'fobi_checklist_taxas.scientific_name',
            'fobi_checklist_taxas.latitude',
            'fobi_checklist_taxas.longitude',
            'fobi_checklist_taxas.created_at',
            'fobi_checklist_taxas.updated_at',
            'taxa_quality_assessments.grade',
            'taxa_quality_assessments.has_media',
            'taxa_quality_assessments.is_wild',
            'taxa_quality_assessments.location_accurate',
            'taxa_quality_assessments.needs_id',
            'taxa_quality_assessments.community_id_level',
            'fobi_users.uname as observer_name',
            'taxas.cname_species',
            'taxas.scientific_name as taxa_scientific_name',
            DB::raw("'fobi' as source")
        ];
    }

    private function getItemImages($item)
    {
        switch ($item->source) {
            case 'fobi':
                return DB::table('fobi_checklist_media')
                    ->where('checklist_id', $item->id)
                    ->where('media_type', 'photo')
                    ->select('id', 'file_path as url')
                    ->get()
                    ->map(function($image) {
                        $image->url = asset('storage/' . $image->url);
                        return $image;
                    });

            case 'burungnesia':
                return DB::table('fobi_checklist_fauna_imgs')
                    ->where('checklist_id', $item->id)
                    ->where('fauna_id', $item->fauna_id)
                    ->select('id', 'images as url')
                    ->get();

            case 'kupunesia':
                return DB::table('fobi_checklist_fauna_imgs_kupnes')
                    ->where('checklist_id', $item->id)
                    ->where('fauna_id', $item->fauna_id)
                    ->select('id', 'images as url')
                    ->get();

            default:
                return collect([]);
        }
    }

    private function getItemAudio($item)
    {
        switch ($item->source) {
            case 'fobi':
                return DB::table('fobi_checklist_media')
                    ->where('checklist_id', $item->id)
                    ->where('media_type', 'audio')
                    ->select('id', 'file_path as url', 'spectrogram')
                    ->first();

            case 'burungnesia':
                return DB::table('fobi_checklist_sounds')
                    ->where('checklist_id', $item->id)
                    ->where('fauna_id', $item->fauna_id)
                    ->select('id', 'sounds as url', 'spectrogram')
                    ->first();

            case 'kupunesia':
                return null; // Kupunesia tidak memiliki audio

            default:
                return null;
        }
    }

    private function getFobiCount($item)
    {
        return DB::table('fobi_checklist_taxas')
            ->where('taxa_id', $item->taxa_id)
            ->count();
    }

    private function getBurungnesiaCount($item)
    {
        if ($item->source === 'burungnesia') {
            return DB::table('fobi_checklist_faunasV1')
                ->where('fauna_id', $item->fauna_id)
                ->count();
        }

        // Jika sumber bukan dari Burungnesia, cari berdasarkan scientific name
        return DB::connection('second')
            ->table('checklist_fauna')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->where('faunas.nameLat', $item->scientific_name)
            ->count();
    }

    private function getKupunesiaCount($item)
    {
        if ($item->source === 'kupunesia') {
            return DB::table('fobi_checklist_faunasv2')
                ->where('fauna_id', $item->fauna_id)
                ->count();
        }

        // Jika sumber bukan dari Kupunesia, cari berdasarkan scientific name
        return DB::connection('third')
            ->table('checklist_fauna')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->where('faunas.nameLat', $item->scientific_name)
            ->count();
    }

    // Method untuk mengambil identifikasi komunitas
    private function getIdentifications($item)
    {
        $tableName = match($item->source) {
            'fobi' => 'taxa_identifications',
            'burungnesia' => 'burungnesia_identifications',
            'kupunesia' => 'kupunesia_identifications',
            default => null
        };

        if (!$tableName) return collect([]);

        return DB::table($tableName)
            ->join('fobi_users', 'fobi_users.id', '=', $tableName.'.user_id')
            ->where('observation_id', $item->id)
            ->select([
                $tableName.'.id',
                $tableName.'.user_id',
                'fobi_users.uname as identifier_name',
                $tableName.'.taxon_id',
                $tableName.'.identification_level',
                $tableName.'.comment',
                $tableName.'.created_at',
                DB::raw('(SELECT COUNT(*) FROM '.$tableName.' WHERE agrees_with_id = '.$tableName.'.id) as agreement_count')
            ])
            ->get();
    }

    // Method untuk mengoptimalkan query dengan eager loading
    private function optimizeQuery($query)
    {
        return $query->select($this->getBaseFields())
                    ->with([
                        'user:id,uname',
                        'media' => function($query) {
                            $query->select('id', 'checklist_id', 'file_path', 'media_type')
                                  ->where('media_type', 'photo')
                                  ->take(5);
                        }
                    ])
                    ->withCount('identifications');
    }

    // Method untuk memformat response
    private function formatResponse($items)
    {
        return $items->map(function($item) {
            return [
                'id' => $item->id,
                'source' => $item->source,
                'scientific_name' => $item->scientific_name,
                'common_name' => $item->cname_species,
                'observer' => [
                    'id' => $item->user_id,
                    'name' => $item->observer_name
                ],
                'location' => [
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude
                ],
                'quality' => [
                    'grade' => $item->grade,
                    'has_media' => $item->has_media,
                    'is_wild' => $item->is_wild,
                    'needs_id' => $item->needs_id
                ],
                'media' => [
                    'images' => $item->images,
                    'audio' => $item->audio
                ],
                'counts' => [
                    'fobi' => $item->fobi_count,
                    'burungnesia' => $item->burungnesia_count,
                    'kupunesia' => $item->kupunesia_count
                ],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });
    }

    // getsuga tenshou
}
