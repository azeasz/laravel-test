<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ObservationShowController extends Controller
{
    public function getObservations(Request $request)
    {
        try {
            // Definisikan nilai ENUM yang valid
            $validGrades = ['research grade', 'needs id', 'low quality id', 'casual'];

            // Validasi request
            $request->validate([
                'grade' => 'nullable|array',
                'grade.*' => ['nullable', Rule::in($validGrades)],
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'data_source' => 'nullable|array',
                'data_source.*' => 'nullable|string',
                'has_media' => 'nullable|boolean',
                'media_type' => 'nullable|string|in:photo,audio',
                'search' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'radius' => 'nullable|numeric|min:1',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $perPage = $request->input('per_page', 100);

// Step 1: Ambil ID observations dengan filter dasar
$baseQuery = DB::table('fobi_checklist_taxas')
->select('fobi_checklist_taxas.id')
->join('taxa_quality_assessments', 'fobi_checklist_taxas.id', '=', 'taxa_quality_assessments.taxa_id');

// Terapkan filter dasar dan pengurutan
$baseQuery->orderBy('fobi_checklist_taxas.created_at', 'desc'); // Tambahkan pengurutan di sini

if ($request->has('search')) {
$search = $request->search;
$baseQuery->where(function($q) use ($search) {
    $q->where('fobi_checklist_taxas.scientific_name', 'like', "%{$search}%")
      ->orWhere('fobi_checklist_taxas.genus', 'like', "%{$search}%")
      ->orWhere('fobi_checklist_taxas.species', 'like', "%{$search}%")
      ->orWhere('fobi_checklist_taxas.family', 'like', "%{$search}%");
});
}

if ($request->has('grade') && is_array($request->grade)) {
$grades = array_map('strtolower', $request->grade);
$baseQuery->whereIn(DB::raw('LOWER(taxa_quality_assessments.grade)'), $grades);
}

// Filter lokasi dan tanggal
if ($request->has('latitude') && $request->has('longitude')) {
$lat = $request->latitude;
$lng = $request->longitude;
$radius = $request->radius ?? 10;

$baseQuery->whereRaw("
    ST_Distance_Sphere(
        point(fobi_checklist_taxas.longitude, fobi_checklist_taxas.latitude),
        point(?, ?)
    ) <= ?
", [$lng, $lat, $radius * 1000]);
}

// Filter tanggal
if ($request->has('start_date')) {
$baseQuery->where('fobi_checklist_taxas.created_at', '>=', $request->start_date);
}
if ($request->has('end_date')) {
$baseQuery->where('fobi_checklist_taxas.created_at', '<=', $request->end_date);
}

// Step 2: Paginate IDs
$observationIds = $baseQuery
->pluck('id'); // Hapus orderBy di sini karena sudah diurutkan di atas

// Step 3: Ambil detail observations dengan eager loading dan tetap urutannya
$observations = DB::table('fobi_checklist_taxas')
->whereIn('fobi_checklist_taxas.id', $observationIds)
->orderByRaw("FIELD(fobi_checklist_taxas.id, " . implode(',', $observationIds->toArray()) . ")") // Tambahkan ini untuk mempertahankan urutan
->join('taxa_quality_assessments', 'fobi_checklist_taxas.id', '=', 'taxa_quality_assessments.taxa_id')
->join('fobi_users', 'fobi_checklist_taxas.user_id', '=', 'fobi_users.id')
->join('taxas', 'fobi_checklist_taxas.taxa_id', '=', 'taxas.id')
->select([
    'fobi_checklist_taxas.id',
    'fobi_checklist_taxas.taxa_id',
    'fobi_checklist_taxas.scientific_name',
    'fobi_checklist_taxas.genus',
    'fobi_checklist_taxas.species',
    'fobi_checklist_taxas.family',
    'taxas.cname_species',
    'fobi_checklist_taxas.latitude',
    'fobi_checklist_taxas.longitude',
    'fobi_checklist_taxas.created_at',
    'taxa_quality_assessments.grade',
    'taxa_quality_assessments.has_media',
    'taxa_quality_assessments.needs_id',
    'fobi_users.uname as observer_name',
    'fobi_users.id as observer_id'
])
->paginate($perPage);

// Step 4: Ambil semua media dalam satu query
$mediaData = DB::table('fobi_checklist_media')
->whereIn('checklist_id', $observations->pluck('id'))
->get()
->groupBy('checklist_id');

// Step 5: Ambil fobi counts dalam satu query
$fobiCounts = DB::table('fobi_checklist_taxas')
->whereIn('taxa_id', $observations->pluck('taxa_id'))
->select('taxa_id', DB::raw('count(*) as count'))
->groupBy('taxa_id')
->pluck('count', 'taxa_id');

// Step 6: Format data
foreach ($observations as $observation) {
$medias = $mediaData[$observation->id] ?? collect();

$observation->images = [];
$observation->audioUrl = null;
$observation->spectrogram = null;

foreach ($medias as $media) {
    if ($media->media_type === 'photo') {
        $observation->images[] = [
            'id' => $media->id,
            'media_type' => 'photo',
            'url' => asset('storage/' . $media->file_path)
        ];
    } else if ($media->media_type === 'audio') {
        $observation->audioUrl = asset('storage/' . $media->file_path);
        $observation->spectrogram = asset('storage/' . $media->spectrogram);
    }
}

$observation->image = !empty($observation->images)
    ? $observation->images[0]['url']
    : asset('images/default-thumbnail.jpg');

$observation->fobi_count = $fobiCounts[$observation->taxa_id] ?? 0;
$observation->source = 'fobi';
}

return response()->json([
'success' => true,
'data' => $observations->items(),
'meta' => [
    'current_page' => $observations->currentPage(),
    'per_page' => $perPage,
    'total' => $observations->total(),
    'last_page' => $observations->lastPage()
],
'links' => [
    'first' => $observations->url(1),
    'last' => $observations->url($observations->lastPage()),
    'prev' => $observations->previousPageUrl(),
    'next' => $observations->nextPageUrl()
]
]);

} catch (\Exception $e) {
Log::error('Error fetching observations: ' . $e->getMessage());
return response()->json([
'success' => false,
'message' => 'Terjadi kesalahan saat mengambil data observasi'
], 500);
}
}
public function getObservationsBurungnesia(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 200);
            $page = $request->input('page', 1);

            // Query untuk data FOBI
            $fobiData = DB::table('fobi_checklists')
                ->join('fobi_checklist_faunasv1', 'fobi_checklists.id', '=', 'fobi_checklist_faunasv1.checklist_id')
                ->join('data_quality_assessments', function($join) {
                    $join->on('fobi_checklists.id', '=', 'data_quality_assessments.observation_id')
                         ->on('fobi_checklist_faunasv1.fauna_id', '=', 'data_quality_assessments.fauna_id');
                })
                ->join('fobi_users', 'fobi_checklists.fobi_user_id', '=', 'fobi_users.id')
                ->joinSub(
                    DB::connection('second')->table('faunas')->select('id', 'nameId', 'nameLat', 'family'),
                    'second_faunas',
                    function($join) {
                        $join->on('fobi_checklist_faunasv1.fauna_id', '=', 'second_faunas.id');
                    }
                )
                ->select(
                    'fobi_checklists.id',
                    'fobi_checklists.latitude',
                    'fobi_checklists.longitude',
                    'fobi_checklist_faunasv1.fauna_id',
                    'fobi_checklist_faunasv1.count',
                    'fobi_checklist_faunasv1.notes',
                    'data_quality_assessments.grade',
                    'data_quality_assessments.has_media',
                    'fobi_users.uname as observer_name',
                    'fobi_users.id as observer_id',
                    'second_faunas.nameId',
                    'second_faunas.nameLat',
                    'second_faunas.family',
                    DB::raw("'fobi' as source"),
                    'fobi_checklists.created_at'
                )
                ->get();

            // Query untuk data Burungnesia
            $burungnesiaData = DB::connection('second')->table('checklists')
                ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->join('users', 'checklists.user_id', '=', 'users.id')
                ->select(
                    'checklists.id',
                    'checklists.latitude',
                    'checklists.longitude',
                    'checklist_fauna.fauna_id',
                    'checklist_fauna.count',
                    'checklist_fauna.notes',
                    DB::raw("'checklist burungnesia' as grade"),
                    DB::raw('0 as has_media'),
                    'users.uname as observer_name',
                    'faunas.nameId',
                    'faunas.nameLat',
                    'faunas.family',
                    DB::raw("'burungnesia' as source"),
                    'checklists.created_at'
                )
                ->get();

            // Log jumlah data
            Log::info('Data counts:', [
                'fobi_count' => $fobiData->count(),
                'burungnesia_count' => $burungnesiaData->count()
            ]);

            // Gabungkan kedua koleksi
            $allData = $fobiData->concat($burungnesiaData)
                ->sortByDesc('created_at');

            // Manual pagination
            $total = $allData->count();
            $items = $allData->forPage($page, $perPage);

            // Proses data gambar dan count
            foreach ($items as $observation) {
                if ($observation->source === 'fobi') {
                    $observation->images = DB::table('fobi_checklist_fauna_imgs')
                        ->where('checklist_id', $observation->id)
                        ->where('fauna_id', $observation->fauna_id)
                        ->select('id', 'images as url')
                        ->get();
                } else {
                    $observation->images = collect([]);
                }

                $observation->fobi_count = DB::table('fobi_checklist_faunasv1')
                    ->where('fauna_id', $observation->fauna_id)
                    ->count();

                $observation->burungnesia_count = DB::connection('second')
                    ->table('checklist_fauna')
                    ->where('fauna_id', $observation->fauna_id)
                    ->count();
            }

            return response()->json([
                'success' => true,
                'data' => $items->values()->all(),
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ],
                'debug' => [
                    'fobi_count' => $fobiData->count(),
                    'burungnesia_count' => $burungnesiaData->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getObservations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data observasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getObservationsKupunesia(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);

            // Query untuk data FOBI
            $fobiData = DB::table('fobi_checklists_kupnes')
                ->join('fobi_checklist_faunasv2', 'fobi_checklists_kupnes.id', '=', 'fobi_checklist_faunasv2.checklist_id')
                ->join('data_quality_assessments_kupnes', function($join) {
                    $join->on('fobi_checklists_kupnes.id', '=', 'data_quality_assessments_kupnes.observation_id')
                         ->on('fobi_checklist_faunasv2.fauna_id', '=', 'data_quality_assessments_kupnes.fauna_id');
                })
                ->join('fobi_users', 'fobi_checklists_kupnes.fobi_user_id', '=', 'fobi_users.id')
                ->joinSub(
                    DB::connection('third')->table('faunas')->select('id', 'nameId', 'nameLat', 'family'),
                    'third_faunas',
                    function($join) {
                        $join->on('fobi_checklist_faunasv2.fauna_id', '=', 'third_faunas.id');
                    }
                )
                ->select(
                    'fobi_checklists_kupnes.id',
                    'fobi_checklists_kupnes.latitude',
                    'fobi_checklists_kupnes.longitude',
                    'fobi_checklist_faunasv2.fauna_id',
                    'fobi_checklist_faunasv2.count',
                    'fobi_checklist_faunasv2.notes',
                    'data_quality_assessments_kupnes.grade',
                    'data_quality_assessments_kupnes.has_media',
                    'fobi_users.uname as observer_name',
                    'fobi_users.id as observer_id',
                    'third_faunas.nameId',
                    'third_faunas.nameLat',
                    'third_faunas.family',
                    DB::raw("'fobi' as source"),
                    'fobi_checklists_kupnes.created_at'
                )
                ->get();

            // Query untuk data Kupunesia
            $kupunesiaData = DB::connection('third')->table('checklists as k_checklists')
                ->join('checklist_fauna as k_checklist_fauna', 'k_checklists.id', '=', 'k_checklist_fauna.checklist_id')
                ->join('faunas as k_faunas', 'k_checklist_fauna.fauna_id', '=', 'k_faunas.id')
                ->join('users as k_users', 'k_checklists.user_id', '=', 'k_users.id')
                ->whereIn('k_faunas.family', [
                    'Papilionidae', 'Pieridae', 'Nymphalidae',
                    'Lycaenidae', 'Hesperiidae', 'Riodinidae'
                ])
                ->select(
                    'k_checklists.id',
                    'k_checklists.latitude',
                    'k_checklists.longitude',
                    'k_checklist_fauna.fauna_id',
                    'k_checklist_fauna.count',
                    'k_checklist_fauna.notes',
                    DB::raw("'checklist kupunesia' as grade"),
                    DB::raw('0 as has_media'),
                    'k_users.uname as observer_name',
                    'k_faunas.nameId',
                    'k_faunas.nameLat',
                    'k_faunas.family',
                    DB::raw("'kupunesia' as source"),
                    'k_checklists.created_at'
                )
                ->get();

            // Log jumlah data dari masing-masing sumber
            Log::info('Data counts:', [
                'fobi_count' => $fobiData->count(),
                'kupunesia_count' => $kupunesiaData->count()
            ]);

            // Gabungkan kedua koleksi
            $allData = $fobiData->concat($kupunesiaData)
                ->sortByDesc('created_at');

            // Manual pagination
            $total = $allData->count();
            $items = $allData->forPage($page, $perPage);

            // Proses data gambar dan count
            foreach ($items as $observation) {
                if ($observation->source === 'fobi') {
                    $observation->images = DB::table('fobi_checklist_fauna_imgs_kupnes')
                        ->where('checklist_id', $observation->id)
                        ->where('fauna_id', $observation->fauna_id)
                        ->select('id', 'images as url')
                        ->get();
                } else {
                    $observation->images = DB::connection('third')
                        ->table('checklist_fauna_imgs')
                        ->where('checklist_id', $observation->id)
                        ->where('fauna_id', $observation->fauna_id)
                        ->select('id', 'images as url')
                        ->get();
                }

                $observation->fobi_count = DB::table('fobi_checklist_faunasv2')
                    ->where('fauna_id', $observation->fauna_id)
                    ->count();

                $observation->kupunesia_count = DB::connection('third')
                    ->table('checklist_fauna as k_checklist_fauna')
                    ->join('faunas as k_faunas', 'k_checklist_fauna.fauna_id', '=', 'k_faunas.id')
                    ->where('k_checklist_fauna.fauna_id', $observation->fauna_id)
                    ->whereIn('k_faunas.family', [
                        'Papilionidae', 'Pieridae', 'Nymphalidae',
                        'Lycaenidae', 'Hesperiidae', 'Riodinidae'
                    ])
                    ->count();
            }

            return response()->json([
                'success' => true,
                'data' => $items->values()->all(),
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ],
                'debug' => [
                    'fobi_count' => $fobiData->count(),
                    'kupunesia_count' => $kupunesiaData->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getObservations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data observasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
