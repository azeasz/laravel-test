<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FobiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getHomeProfile($id)
    {
        try {
            // Tambahkan log untuk debugging
            \Log::info('Getting home profile for user:', ['id' => $id]);

            // Ambil data user dengan path lengkap untuk profile_picture
            $user = FobiUser::select(
                'id',
                'fname',
                'lname',
                'uname',
                'email',
                'phone',
                'organization',
                'bio',
                DB::raw("CASE
                    WHEN profile_picture IS NOT NULL
                    THEN CONCAT('/storage/', REPLACE(profile_picture, '/storage/', ''))
                    ELSE NULL
                END as profile_picture"),
                'created_at'
            )->where('id', $id)->first();

            // Log data user untuk debugging
            \Log::info('User data:', ['user' => $user]);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan'
                ], 404);
            }

            // Hitung total observasi
            $observationCount = DB::table('fobi_checklist_taxas')
                ->where('user_id', $id)
                ->count();

            // Hitung total spesies
            $speciesCount = DB::table('fobi_checklist_taxas')
                ->where('user_id', $id)
                ->distinct('taxa_id')
                ->count();

            // Hitung total identifikasi
            $identificationCount = DB::table('taxa_identifications')
                ->where('user_id', $id)
                ->count();

            // Ambil data followers
            $followers = FobiUser::select('id', 'uname', 'profile_picture')
                ->whereIn('id', function($query) use ($id) {
                    $query->select('follower_id')
                        ->from('user_followers')
                        ->where('user_id', $id);
                        })->take(4)->get();

            // Ambil data following
            $following = FobiUser::select('id', 'uname', 'profile_picture')
                ->whereIn('id', function($query) use ($id) {
                    $query->select('user_id')
                        ->from('user_followers')
                        ->where('follower_id', $id);
                })->take(4)->get();

            return response()->json([
               'success' => true,
               'data' => [
                   'user' => $user,
                   'stats' => [
                       'observasi' => number_format($observationCount),
                       'spesies' => number_format($speciesCount),
                       'identifikasi' => number_format($identificationCount)
                   ],
                   'social' => [
                       'followers' => $followers,
                       'following' => $following,
                       'followerCount' => DB::table('user_followers')->where('user_id', $id)->count(),
                       'followingCount' => DB::table('user_followers')->where('follower_id', $id)->count()
                   ]
               ]
           ]);
        } catch (\Exception $e) {
           \Log::error('Error in getHomeProfile:', [
               'id' => $id,
               'error' => $e->getMessage()
           ]);
            return response()->json([
               'success' => false,
               'message' => 'Terjadi kesalahan saat mengambil data profil'
           ], 500);
       }
   }
   public function getObservations($id, Request $request)
{
    try {
        \Log::info('Received request for observations:', [
            'id' => $id,
            'page' => $request->query('page'),
            'search' => $request->query('search')
        ]);

        $user = DB::table('fobi_users')->find($id);
        if (!$user) {
            \Log::error('User tidak ditemukan:', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Ambil observasi dari fobi_checklist_taxas
        $fobiObservations = DB::table('fobi_checklist_taxas as fct')
            ->join('taxas as t', 't.id', '=', 'fct.taxa_id')
            ->where('fct.user_id', $id)
            ->select(
                'fct.id',
                'fct.photo_url',
                't.nama_latin',
                't.nama_umum',
                'fct.date',
                'fct.created_at',
                DB::raw("'fobi' as source")
            );

        // Ambil observasi burung
        $birdObservations = DB::table('fobi_checklists as fc')
            ->join('burungnesia.fauna as bf', 'bf.id', '=', 'fc.fauna_id')
            ->where('fc.fobi_user_id', $id)
            ->select(
                'fc.id',
                'fc.photo_url',
                'bf.nama_latin',
                'bf.nama_umum',
                'fc.created_at',
                DB::raw("'burung' as source")
            );

        // Ambil observasi kupu-kupu
        $butterflyObservations = DB::table('fobi_checklists_kupnes as fck')
            ->join('kupunesia.fauna as kf', 'kf.id', '=', 'fck.fauna_id')
            ->where('fck.fobi_user_id', $id)
            ->select(
                'fck.id',
                'fck.photo_url',
                'kf.nama_latin',
                'kf.nama_umum',
                'fck.created_at',
                DB::raw("'kupu-kupu' as source")
            );

        // Gabungkan semua observasi dan urutkan berdasarkan tanggal
        $allObservations = $fobiObservations
            ->union($birdObservations)
            ->union($butterflyObservations)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return response()->json([
           'success' => true,
           'data' => $allObservations
       ]);
    } catch (\Exception $e) {
       return response()->json([
           'success' => false,
           'message' => 'Gagal mengambil data observasi'
       ], 500);
   }
}

public function getIdentifications($id)
{
   try {
       $identifications = DB::table('taxa_identifications as ti')
           ->join('taxas as t', 't.id', '=', 'ti.taxa_id')
           ->join('fobi_users as fu', 'fu.id', '=', 'ti.observer_id')
           ->where('ti.user_id', $id)
           ->select(
               'ti.id',
               'ti.photo_url',
               't.nama_latin',
               't.nama_umum',
               'ti.created_at',
               'fu.uname as observer_username',
               'ti.status',
               'ti.notes'
           )
           ->orderBy('ti.created_at', 'desc')
           ->paginate(12);
        return response()->json([
           'success' => true,
           'data' => $identifications
       ]);
    } catch (\Exception $e) {
       return response()->json([
           'success' => false,
           'message' => 'Gagal mengambil data identifikasi'
       ], 500);
   }
}

public function getSpecies($id)
{
   try {
       $user = FobiUser::findOrFail($id);
        // Ambil spesies dari FOBI
       $fobiSpecies = DB::table('fobi_checklist_taxas as fct')
           ->join('taxas as t', 't.id', '=', 'fct.taxa_id')
           ->where('fct.user_id', $id)
           ->select(
               'fct.id',
               't.id',
               't.nama_latin',
               't.nama_umum',
               DB::raw('COUNT(*) as jumlah_observasi'),
               DB::raw("'fobi' as source")
           )
           ->groupBy('t.id', 't.nama_latin', 't.nama_umum');
        // Ambil spesies burung
       $birdSpecies = DB::table('fobi_checklists as fc')
           ->join('burungnesia.fauna as bf', 'bf.id', '=', 'fc.fauna_id')
           ->where('fc.fobi_user_id', $id)
           ->select(
               'fc.id',
               'bf.id',
               'bf.nama_latin',
               'bf.nama_umum',
               DB::raw('COUNT(*) as jumlah_observasi'),
               DB::raw("'burung' as source")
           )
           ->groupBy('bf.id', 'bf.nama_latin', 'bf.nama_umum');
        // Ambil spesies kupu-kupu
       $butterflySpecies = DB::table('fobi_checklists_kupnes as fck')
           ->join('kupunesia.fauna as kf', 'kf.id', '=', 'fck.fauna_id')
           ->where('fck.fobi_user_id', $id)
           ->select(
               'fck.id',
               'kf.id',
               'kf.nama_latin',
               'kf.nama_umum',
               DB::raw('COUNT(*) as jumlah_observasi'),
               DB::raw("'kupu-kupu' as source")
           )
           ->groupBy('kf.id', 'kf.nama_latin', 'kf.nama_umum');
        // Gabungkan semua spesies
       $allSpecies = $fobiSpecies
           ->union($birdSpecies)
           ->union($butterflySpecies)
           ->orderBy('jumlah_observasi', 'desc')
           ->paginate(12);
        return response()->json([
           'success' => true,
           'data' => $allSpecies
       ]);
    } catch (\Exception $e) {
       return response()->json([
           'success' => false,
           'message' => 'Gagal mengambil data spesies'
       ], 500);
   }
}

private function processProfileImage($file)
{
    try {
        $uploadPath = storage_path('app/public/profile_pictures');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $image = Image::make($file->getRealPath());

        // Resize gambar dengan mempertahankan aspek ratio
        $image->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $fileName = time() . '_' . uniqid() . '.jpg';
        $relativePath = 'profile_pictures/' . $fileName;
        $fullPath = storage_path('app/public/' . $relativePath);

        // Simpan gambar dengan kualitas 80%
        $image->save($fullPath, 80);

        return [
            'success' => true,
            'path' => '/storage/' . $relativePath
        ];

    } catch (\Exception $e) {
        \Log::error('Error memproses foto profil: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

public function update(Request $request)
{
    try {
        \Log::info('Profile update request:', $request->all());

        $user = auth()->user();

        // Update fields biasa
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->uname = $request->uname;
        $user->organization = $request->organization;
        $user->phone = $request->phone;
        $user->bio = $request->bio;

        // Handle profile picture jika ada
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Simpan file baru
            $file->storeAs('public/profile_pictures', $filename);

            // Update database dengan path baru
            $user->profile_picture = 'profile_pictures/' . $filename;

            // Hapus file lama jika ada
            if ($user->getOriginal('profile_picture')) {
                Storage::delete('public/' . $user->getOriginal('profile_picture'));
            }
        }

        $user->save();

        \Log::info('Profile updated successfully for user:', ['id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'data' => $user
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating profile:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate profil',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function getUserActivities($id, Request $request)
{
    try {
        $period = $request->query('period', 'year');

        // Set rentang waktu berdasarkan periode
        $endDate = now();
        $startDate = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subYear(),
        };

        // Aktivitas FOBI dengan error handling
        $fobiActivities = DB::table('fobi_checklist_taxas')
            ->where('user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("'fobi' as source")
            )
            ->groupBy(DB::raw('DATE(created_at)'));

        // Aktivitas Burungnesia dengan error handling
        $birdActivities = DB::table('fobi_checklists')
            ->where('fobi_user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("'bird' as source")
            )
            ->groupBy(DB::raw('DATE(created_at)'));

        // Aktivitas Kupunesia dengan error handling
        $butterflyActivities = DB::table('fobi_checklists_kupnes')
            ->where('fobi_user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("'butterfly' as source")
            )
            ->groupBy(DB::raw('DATE(created_at)'));

        // Aktivitas Identifikasi dengan error handling
        $identificationActivities = DB::table('taxa_identifications')
            ->where('user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("'identification' as source")
            )
            ->groupBy(DB::raw('DATE(created_at)'));

        // Gabungkan semua data dengan error handling
        $allActivities = collect();

        try {
            $allActivities = $allActivities->concat($fobiActivities->get());
        } catch (\Exception $e) {
            \Log::error('Error getting FOBI activities: ' . $e->getMessage());
        }

        try {
            $allActivities = $allActivities->concat($birdActivities->get());
        } catch (\Exception $e) {
            \Log::error('Error getting bird activities: ' . $e->getMessage());
        }

        try {
            $allActivities = $allActivities->concat($butterflyActivities->get());
        } catch (\Exception $e) {
            \Log::error('Error getting butterfly activities: ' . $e->getMessage());
        }

        try {
            $allActivities = $allActivities->concat($identificationActivities->get());
        } catch (\Exception $e) {
            \Log::error('Error getting identification activities: ' . $e->getMessage());
        }

        // Format data untuk grafik dengan pengecekan null
        $formattedData = $allActivities
            ->groupBy('date')
            ->map(function ($items) {
                return [
                    'date' => $items->first()->date,
                    'sources' => [
                        'fobi' => $items->where('source', 'fobi')->sum('count') ?? 0,
                        'bird' => $items->where('source', 'bird')->sum('count') ?? 0,
                        'butterfly' => $items->where('source', 'butterfly')->sum('count') ?? 0,
                        'identification' => $items->where('source', 'identification')->sum('count') ?? 0
                    ]
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getUserActivities: ' . $e->getMessage(), [
            'id' => $id,
            'period' => $period ?? 'unknown',
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data aktivitas',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function getTopTaxa($id)
{
    try {
        // Top 5 observasi - Optimasi query
        $topObservations = DB::table('fobi_checklist_taxas as fct')
            ->select(
                'fct.id as checklist_id',
                'taxas.id',
                'taxas.scientific_name',
                'taxas.genus',
                'taxas.family',
                DB::raw('COUNT(DISTINCT fct.id) as count'),
                DB::raw("'fobi' as source")
            )
            ->join('taxas', 'fct.taxa_id', '=', 'taxas.id')
            ->where('fct.user_id', $id)
            ->groupBy('fct.id', 'taxas.id', 'taxas.scientific_name', 'taxas.genus', 'taxas.family')
            ->orderByDesc('count')
            ->limit(5);

        // Ambil media untuk setiap observasi
        $observations = $topObservations->get()->map(function($observation) {
            // Ambil media untuk observasi ini
            $media = DB::table('fobi_checklist_media')
                ->where('checklist_id', $observation->checklist_id)
                ->where('status', 0)
                ->select(
                    'id',
                    'file_path',
                    'spectrogram',
                    'media_type'
                )
                ->get();

            // Tambahkan URL media
            $observation->media = $media->map(function($item) {
                return [
                    'id' => $item->id,
                    'url' => asset('storage/' . $item->file_path),
                    'spectrogram_url' => $item->spectrogram ? asset('storage/' . $item->spectrogram) : null,
                    'type' => $item->media_type
                ];
            });

            return $observation;
        });

        // Top 5 Identifikasi - Optimasi query
        $topIdentifications = DB::table('taxa_identifications as ti')
            ->select(
                DB::raw('COALESCE(ti.checklist_id, ti.burnes_checklist_id, ti.kupnes_checklist_id) as checklist_id'),
                'taxas.id',
                'taxas.scientific_name',
                'taxas.genus',
                'taxas.family',
                DB::raw('COUNT(DISTINCT ti.id) as count'),
                DB::raw("CASE
                    WHEN ti.burnes_checklist_id IS NOT NULL THEN 'burungnesia'
                    WHEN ti.kupnes_checklist_id IS NOT NULL THEN 'kupunesia'
                    ELSE 'fobi'
                END as source")
            )
            ->join('taxas', 'ti.taxon_id', '=', 'taxas.id')
            ->where('ti.user_id', $id)
            ->groupBy(
                'ti.checklist_id',
                'ti.burnes_checklist_id',
                'ti.kupnes_checklist_id',
                'taxas.id',
                'taxas.scientific_name',
                'taxas.genus',
                'taxas.family'
            )
            ->orderByDesc('count')
            ->limit(5);

        // Ambil media untuk setiap identifikasi
        $identifications = $topIdentifications->get()->map(function($identification) {
            if ($identification->source === 'fobi') {
                $media = DB::table('fobi_checklist_media')
                    ->where('checklist_id', $identification->checklist_id)
                    ->where('status', 0)
                    ->select(
                        'id',
                        'file_path',
                        'spectrogram',
                        'media_type'
                    )
                    ->get();

                $identification->media = $media->map(function($item) {
                    return [
                        'id' => $item->id,
                        'url' => asset('storage/' . $item->file_path),
                        'spectrogram_url' => $item->spectrogram ? asset('storage/' . $item->spectrogram) : null,
                        'type' => $item->media_type
                    ];
                });
            }
            return $identification;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'observations' => $observations,
                'identifications' => $identifications
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getTopTaxa: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data taksa teratas',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function getUserObservations($id, Request $request)
{
    try {
        $user = DB::table('fobi_users')->find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $search = strtolower($request->query('search', ''));
        $searchType = $request->query('search_type', 'all');
        $dateFilter = $request->query('date', '');
        $perPage = $request->query('per_page', 5);
        $page = $request->query('page', 1);
        $isMapRequest = $request->query('map', false);

        // Query untuk observasi FOBI
        $fobiObservations = DB::table('fobi_checklist_taxas as fct')
            ->select([
                DB::raw('DISTINCT fct.id'),
                'fct.scientific_name as nama_latin',
                't.cname_species as nama_umum',
                'fct.latitude',
                'fct.longitude',
                'fct.date as observation_date',
                DB::raw('(SELECT file_path FROM fobi_checklist_media WHERE checklist_id = fct.id ORDER BY id ASC LIMIT 1) as photo_url'),
                'fct.family',
                'fct.order as ordo',
                'fct.created_at',
                DB::raw("'fobi' as source")
            ])
            ->join('taxas as t', 't.id', '=', 'fct.taxa_id')
            ->where('fct.user_id', $id);

        $allObservations = collect($fobiObservations->get());

        // Cek dan tambahkan data Burungnesia
        if ($user->burungnesia_user_id) {
            try {
                // Query untuk Burungnesia dari tabel FOBI
                $birdObservationsFobi = DB::table('fobi_checklists as fc')
                    ->select([
                        DB::raw('DISTINCT fc.id'),
                        'f.nameLat as nama_latin',
                        'f.nameId as nama_umum',
                        'fc.latitude',
                        'fc.longitude',
                        'fc.tgl_pengamatan as observation_date',
                        DB::raw('(SELECT images FROM fobi_checklist_fauna_imgs WHERE checklist_id = fc.id ORDER BY id ASC LIMIT 1) as photo_url'),
                        'f.family',
                        'of.ordo',
                        'fc.created_at',
                        DB::raw("'bird' as source")
                    ])
                    ->join('fobi_checklist_faunasv1 as fcf', 'fc.id', '=', 'fcf.checklist_id')
                    ->join('faunas as f', 'fcf.fauna_id', '=', 'f.id')
                    ->leftJoin('order_faunas as of', 'f.family', '=', 'of.famili')
                    ->where('fc.fobi_user_id', $id);

                $allObservations = $allObservations->concat($birdObservationsFobi->get());

                // Query untuk Burungnesia dari database second
                if (DB::connection('second')->getDatabaseName()) {
                    $birdObservationsSecond = DB::connection('second')
                        ->table('checklists as c')
                        ->select([
                            DB::raw('DISTINCT c.id'),
                            'f.nameLat as nama_latin',
                            'f.nameId as nama_umum',
                            'c.latitude',
                            'c.longitude',
                            'c.tgl_pengamatan as observation_date',
                            DB::raw('NULL as photo_url'),
                            'f.family',
                            'of.ordo',
                            'c.created_at',
                            DB::raw("'bird' as source")
                        ])
                        ->join('checklist_fauna as cf', 'c.id', '=', 'cf.checklist_id')
                        ->join('faunas as f', 'cf.fauna_id', '=', 'f.id')
                        ->leftJoin('order_faunas as of', 'f.family', '=', 'of.famili')
                        ->where('c.user_id', $user->burungnesia_user_id);

                    $allObservations = $allObservations->concat($birdObservationsSecond->get());
                }
            } catch (\Exception $e) {
                \Log::warning('Error fetching bird observations: ' . $e->getMessage());
            }
        }

        // Cek dan tambahkan data Kupunesia
        if ($user->kupunesia_user_id) {
            try {
                // Query untuk Kupunesia dari tabel FOBI
                $butterflyObservationsFobi = DB::table('fobi_checklists_kupnes as fck')
                    ->select([
                        DB::raw('DISTINCT fck.id'),
                        'f.nameLat as nama_latin',
                        'f.nameId as nama_umum',
                        'fck.latitude',
                        'fck.longitude',
                        'fck.tgl_pengamatan as observation_date',
                        DB::raw('(SELECT images FROM fobi_checklist_fauna_imgs_kupnes WHERE checklist_id = fck.id ORDER BY id ASC LIMIT 1) as photo_url'),
                        'f.family',
                        DB::raw("'butterfly' as source"),
                        'fck.created_at'
                    ])
                    ->join('fobi_checklist_faunasv2 as fcf', 'fck.id', '=', 'fcf.checklist_id')
                    ->join('faunas as f', 'fcf.fauna_id', '=', 'f.id')
                    ->where('fck.fobi_user_id', $id);

                $allObservations = $allObservations->concat($butterflyObservationsFobi->get());

                // Query untuk Kupunesia dari database third
                if (DB::connection('third')->getDatabaseName()) {
                    $butterflyObservationsThird = DB::connection('third')
                        ->table('checklists as c')
                        ->select([
                            DB::raw('DISTINCT c.id'),
                            'f.nameLat as nama_latin',
                            'f.nameId as nama_umum',
                            'c.latitude',
                            'c.longitude',
                            'c.tgl_pengamatan as observation_date',
                            DB::raw('(SELECT images FROM checklist_fauna_imgs WHERE checklist_id = c.id ORDER BY id ASC LIMIT 1) as photo_url'),
                            'f.family',
                            DB::raw("'butterfly' as source"),
                            'c.created_at'
                        ])
                        ->join('checklist_fauna as cf', 'c.id', '=', 'cf.checklist_id')
                        ->join('faunas as f', 'cf.fauna_id', '=', 'f.id')
                        ->where('c.user_id', $user->kupunesia_user_id);

                    $allObservations = $allObservations->concat($butterflyObservationsThird->get());
                }
            } catch (\Exception $e) {
                \Log::warning('Error fetching butterfly observations: ' . $e->getMessage());
            }
        }

        // Filter berdasarkan pencarian jika bukan request map
        if (!$isMapRequest && ($search || $dateFilter)) {
            $allObservations = $allObservations->filter(function($item) use ($search, $searchType, $dateFilter) {
                // Filter tanggal
                if ($dateFilter) {
                    $itemDate = substr($item->observation_date, 0, 10); // Ambil hanya bagian YYYY-MM-DD
                    if ($itemDate !== $dateFilter) {
                        return false;
                    }
                }

                // Filter pencarian teks
                if ($search) {
                    switch ($searchType) {
                        case 'species':
                            return str_contains(strtolower($item->nama_latin), $search) ||
                                   str_contains(strtolower($item->nama_umum), $search);
                        case 'location':
                            // Implementasi pencarian lokasi jika diperlukan
                            return true;
                        case 'date':
                            return str_contains(strtolower($item->observation_date), $search);
                        default:
                            return str_contains(strtolower($item->nama_latin), $search) ||
                                   str_contains(strtolower($item->nama_umum), $search) ||
                                   str_contains(strtolower($item->observation_date), $search);
                    }
                }

                return true;
            });
        }

        // Urutkan berdasarkan tanggal terbaru
        $allObservations = $allObservations->sortByDesc('observation_date');

        if ($isMapRequest) {
            // Jika request untuk map, kembalikan semua data
            return response()->json([
                'success' => true,
                'data' => $allObservations->values()
            ]);
        }

        // Jika bukan request map, lakukan paginasi untuk tabel
        $total = $allObservations->count();
        $paginatedObservations = $allObservations->forPage($page, $perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'observations' => $paginatedObservations->values(),
                'total' => $total,
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => ceil($total / $perPage)
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getUserObservations:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data observasi'
        ], 500);
    }
}

private function getLocationName($latitude, $longitude)
{
    try {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'header' => 'User-Agent: FOBI/1.0'
            ]
        ]));

        $data = json_decode($response);
        return $data->display_name ?? null;
    } catch (\Exception $e) {
        return null;
    }
}

public function getSearchSuggestions(Request $request)
{
    try {
        $query = $request->get('query', '');
        $type = $request->get('type', 'species');

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        if ($type === 'species') {
            // Cari di tabel taxas
            $taxaSuggestions = DB::table('taxas')
                ->where(function($q) use ($query) {
                    $q->where('scientific_name', 'LIKE', "%{$query}%")
                      ->orWhere('cname_species', 'LIKE', "%{$query}%");
                })
                ->select(
                    'id',
                    'scientific_name',
                    'cname_species as common_name',
                    DB::raw("'taxa' as type")
                )
                ->take(5);

            // Cari di database burungnesia (second)
            try {
                $birdSuggestions = DB::connection('second')
                    ->table('faunas')
                    ->where(function($q) use ($query) {
                        $q->where('nameLat', 'LIKE', "%{$query}%")
                          ->orWhere('nameId', 'LIKE', "%{$query}%");
                    })
                    ->select(
                        'id',
                        'nameLat as scientific_name',
                        'nameId as common_name',
                        DB::raw("'bird' as type")
                    )
                    ->take(5);

                $taxaSuggestions->union($birdSuggestions);
            } catch (\Exception $e) {
                \Log::error('Error querying bird database: ' . $e->getMessage());
            }

            // Cari di database kupunesia (third)
            try {
                $butterflySuggestions = DB::connection('third')
                    ->table('faunas')
                    ->where(function($q) use ($query) {
                        $q->where('nameLat', 'LIKE', "%{$query}%")
                          ->orWhere('nameId', 'LIKE', "%{$query}%");
                    })
                    ->select(
                        'id',
                        'nameLat as scientific_name',
                        'nameId as common_name',
                        DB::raw("'butterfly' as type")
                    )
                    ->take(5);

                $taxaSuggestions->union($butterflySuggestions);
            } catch (\Exception $e) {
                \Log::error('Error querying butterfly database: ' . $e->getMessage());
            }

            $suggestions = $taxaSuggestions->get();
        } else {
            // Suggestions lokasi dari Nominatim
            $suggestions = $this->getLocationSuggestions($query);
        }

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getSearchSuggestions: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mendapatkan saran pencarian'
        ], 500);
    }
}

private function getLocationSuggestions($query)
{
    try {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query) . "&limit=5";
        $response = file_get_contents($url, false, stream_context_create([
            'http' => ['header' => 'User-Agent: FOBI/1.0']
        ]));

        return collect(json_decode($response))->map(function($item) {
            return [
                'id' => $item->place_id,
                'name' => $item->display_name,
                'lat' => $item->lat,
                'lon' => $item->lon
            ];
        });
    } catch (\Exception $e) {
        return collect([]);
    }
}

public function getGridObservations($id, Request $request)
{
    try {
        $zoom = $request->get('zoom', 5);
        $bounds = json_decode($request->get('bounds'), true);
        $search = strtolower($request->get('search', ''));
        $dateFilter = $request->get('date', '');
        $searchType = $request->get('search_type', 'all');

        // Tentukan ukuran grid berdasarkan zoom level
        $gridSize = match(true) {
            $zoom >= 12 => 0.01, // ~1km
            $zoom >= 10 => 0.05, // ~5km
            $zoom >= 8 => 0.1,   // ~10km
            default => 0.5,      // ~50km
        };

        // Query untuk FOBI
        $fobiObservations = DB::table('fobi_checklist_taxas as fct')
            ->select([
                'fct.id',
                'fct.scientific_name as nama_latin',
                't.cname_species as nama_umum',
                'fct.latitude',
                'fct.longitude',
                'fct.date as observation_date',
                'fct.family',
                DB::raw('(SELECT file_path FROM fobi_checklist_media WHERE checklist_id = fct.id LIMIT 1) as photo_url'),
                DB::raw("'fobi' as source"),
                'l.name as location_name'
            ])
            ->join('taxas as t', 't.id', '=', 'fct.taxa_id')
            ->leftJoin('locations as l', 'l.id', '=', 'fct.location_id')
            ->where('fct.user_id', $id);

        // Query untuk Burungnesia
        $birdObservations = DB::table('bird_observations as bo')
            ->select([
                'bo.id',
                'bo.scientific_name as nama_latin',
                'bo.common_name as nama_umum',
                'bo.latitude',
                'bo.longitude',
                'bo.observation_date',
                'bo.family',
                'bo.image_url as photo_url',
                DB::raw("'bird' as source"),
                'bo.location_name'
            ])
            ->where('bo.user_id', $id);

        // Query untuk Kupunesia
        $butterflyObservations = DB::table('butterfly_observations as bfo')
            ->select([
                'bfo.id',
                'bfo.scientific_name as nama_latin',
                'bfo.common_name as nama_umum',
                'bfo.latitude',
                'bfo.longitude',
                'bfo.observation_date',
                'bfo.family',
                'bfo.image_url as photo_url',
                DB::raw("'butterfly' as source"),
                'bfo.location_name'
            ])
            ->where('bfo.user_id', $id);

        // Gabungkan semua query
        $observations = $fobiObservations
            ->union($birdObservations)
            ->union($butterflyObservations);

        // Filter bounds jika ada
        if ($bounds) {
            $observations = $observations->whereBetween('latitude',
                [$bounds['_southWest']['lat'], $bounds['_northEast']['lat']])
                ->whereBetween('longitude',
                    [$bounds['_southWest']['lng'], $bounds['_northEast']['lng']]);
        }

        $observations = $observations->get();

        // Filter hasil
        if ($search || $dateFilter) {
            $observations = $observations->filter(function($obs) use ($search, $searchType, $dateFilter) {
                // Filter tanggal
                if ($dateFilter) {
                    $obsDate = substr($obs->observation_date, 0, 10);
                    if ($obsDate !== $dateFilter) {
                        return false;
                    }
                }

                // Filter berdasarkan tipe pencarian
                if ($search) {
                    switch ($searchType) {
                        case 'species':
                            return str_contains(strtolower($obs->nama_latin), $search) ||
                                   str_contains(strtolower($obs->nama_umum), $search);

                        case 'family':
                            return str_contains(strtolower($obs->family ?? ''), $search);

                        case 'location':
                            return str_contains(strtolower($obs->location_name ?? ''), $search);

                        case 'source':
                            $sourceName = match($obs->source) {
                                'bird' => 'burungnesia',
                                'butterfly' => 'kupunesia',
                                'fobi' => 'fobi',
                                default => strtolower($obs->source)
                            };
                            return str_contains($sourceName, $search);

                        case 'date':
                            return str_contains($obs->observation_date, $search);

                        case 'all':
                        default:
                            return str_contains(strtolower($obs->nama_latin), $search) ||
                                   str_contains(strtolower($obs->nama_umum), $search) ||
                                   str_contains(strtolower($obs->family ?? ''), $search) ||
                                   str_contains(strtolower($obs->location_name ?? ''), $search) ||
                                   str_contains($obs->observation_date, $search) ||
                                   str_contains(strtolower($obs->source), $search);
                    }
                }

                return true;
            });
        }

        // Buat grid
        $grid = [];
        foreach ($observations as $obs) {
            $latGrid = floor($obs->latitude / $gridSize) * $gridSize;
            $lonGrid = floor($obs->longitude / $gridSize) * $gridSize;
            $gridKey = "{$latGrid},{$lonGrid}";

            if (!isset($grid[$gridKey])) {
                $grid[$gridKey] = [
                    'center' => [
                        'lat' => $latGrid + ($gridSize/2),
                        'lng' => $lonGrid + ($gridSize/2)
                    ],
                    'count' => 0,
                    'species' => [],
                    'observations' => [],
                    'size' => $gridSize
                ];
            }

            $grid[$gridKey]['count']++;
            $grid[$gridKey]['species'][] = $obs->nama_latin;
            $grid[$gridKey]['species'] = array_unique($grid[$gridKey]['species']);
            $grid[$gridKey]['observations'][] = [
                'id' => $obs->id,
                'nama_latin' => $obs->nama_latin,
                'nama_umum' => $obs->nama_umum,
                'source' => $obs->source,
                'date' => $obs->observation_date,
                'family' => $obs->family ?? null,
                'photo_url' => $obs->photo_url ?? null,
                'location_name' => $obs->location_name ?? null,
                'latitude' => $obs->latitude,
                'longitude' => $obs->longitude
            ];
        }

        return response()->json([
            'success' => true,
            'data' => array_values($grid)
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getGridObservations:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mendapatkan data grid'
        ], 500);
    }
}

public function syncPlatformEmail(Request $request, $platform)
{
    try {
        \Log::info('Received sync request:', [
            'platform' => $platform,
            'request_data' => $request->all()
        ]);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'recaptcha_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verifikasi reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $recaptchaSecret,
            'response' => $request->recaptcha_token,
        ]);

        $recaptchaResult = $response->json();
        if (!$recaptchaResult['success'] || $recaptchaResult['score'] < 0.5) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan gagal'
            ], 400);
        }

        $user = auth()->user();

        // Cek dan dapatkan user ID dari platform tujuan
        $platformUserId = null;
        if ($platform === 'burungnesia') {
            $platformUser = DB::connection('second')
                ->table('users')
                ->where('email', $request->email)
                ->first();
            $platformUserId = $platformUser ? $platformUser->id : null;
        } else {
            $platformUser = DB::connection('third')
                ->table('users')
                ->where('email', $request->email)
                ->first();
            $platformUserId = $platformUser ? $platformUser->id : null;
        }

        if (!$platformUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Email belum terdaftar di platform tujuan'
            ], 400);
        }

        // Update user dengan email dan token baru
        $verificationToken = Str::random(60);
        $user->{$platform.'_email'} = $request->email;
        $user->{$platform.'_email_verification_token'} = $verificationToken;
        $user->{$platform.'_user_id'} = $platformUserId;
        $user->save();

        // Kirim email verifikasi
        Mail::to($request->email)->send(new VerifyEmail($user, $platform.'_email_verification_token'));

        return response()->json([
            'success' => true,
            'message' => 'Link verifikasi telah dikirim ke email Anda'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in syncPlatformEmail:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menghubungkan akun',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function getUserProfile()
{
    try {
        $user = auth()->user();

        // Tambahkan log untuk debugging
        \Log::info('Getting user profile for:', ['user_id' => $user->id]);

        // Ambil data user dengan path lengkap untuk profile_picture
        $userData = FobiUser::select(
            'id',
            'fname',
            'lname',
            'uname',
            'email',
            'phone',
            'organization',
            'bio',
            DB::raw("CASE
                WHEN profile_picture IS NOT NULL
                THEN CONCAT('/storage/', REPLACE(profile_picture, '/storage/', ''))
                ELSE NULL
            END as profile_picture"),
            'burungnesia_email',
            'burungnesia_email_verified_at',
            'kupunesia_email',
            'kupunesia_email_verified_at',
            'created_at'
        )->where('id', $user->id)->first();

        // Log data user untuk debugging
        \Log::info('User data retrieved:', ['data' => $userData]);

        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Jika ada profile_picture, format pathnya
        if ($userData->profile_picture) {
            // Hapus /storage/ dari awal path jika ada
            $userData->profile_picture = ltrim($userData->profile_picture, '/storage/');
        }

        // Log untuk debugging
        \Log::info('User data retrieved:', ['data' => $userData->toArray()]);

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getUserProfile:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data profil',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function resendPlatformVerification(Request $request, $platform)
{
    try {
        $user = auth()->user();

        // Validasi platform
        if (!in_array($platform, ['burungnesia', 'kupunesia'])) {
            return response()->json([
                'success' => false,
                'message' => 'Platform tidak valid'
            ], 400);
        }

        // Cek apakah email sudah ada
        $platformEmail = $user->{$platform.'_email'};
        if (!$platformEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Email belum terdaftar'
            ], 400);
        }

        // Cek apakah sudah terverifikasi
        if ($user->{$platform.'_email_verified_at'}) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terverifikasi'
            ], 400);
        }

        // Generate token baru
        $verificationToken = Str::random(60);
        $user->{$platform.'_email_verification_token'} = $verificationToken;
        $user->save();

        // Kirim email verifikasi
        Mail::to($platformEmail)->send(new VerifyEmail($user, $platform.'_email_verification_token'));

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in resendPlatformVerification:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim ulang email verifikasi',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function unlinkPlatformAccount(Request $request, $platform)
{
    try {
        // Validasi platform
        if (!in_array($platform, ['burungnesia', 'kupunesia'])) {
            return response()->json([
                'success' => false,
                'message' => 'Platform tidak valid'
            ], 400);
        }

        $user = auth()->user();

        // Reset semua field terkait platform
        $user->{$platform.'_email'} = null;
        $user->{$platform.'_email_verified_at'} = null;
        $user->{$platform.'_email_verification_token'} = null;
        $user->{$platform.'_user_id'} = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dilepaskan'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in unlinkPlatformAccount:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal melepaskan akun',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function updateEmail(Request $request)
{
    try {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:fobi_users,email,' . auth()->id(),
            'password' => 'required|string',
            'recaptcha_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Dapatkan user yang sedang login
        $user = auth()->user();

        // Pastikan user ditemukan
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Verifikasi reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $recaptchaSecret,
            'response' => $request->recaptcha_token,
        ]);

        if (!$response->json()['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan gagal'
            ], 400);
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 400);
        }

        // Generate token verifikasi
        $verificationToken = Str::random(60);

        // Update email dan token
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->email_verification_token = $verificationToken;
        $user->save();

        // Kirim email verifikasi
        Mail::to($request->email)->send(new VerifyEmail($user, 'email_verification_token'));

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diupdate. Silakan verifikasi email baru Anda.',
            'data' => [
                'email' => $request->email,
                'redirectTo' => '/verification-pending',
                'state' => [
                    'email' => $request->email,
                    'hasBurungnesia' => !is_null($user->burungnesia_email),
                    'hasKupunesia' => !is_null($user->kupunesia_email)
                ]
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating email:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate email'
        ], 500);
    }
}

public function deleteAccount(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'recaptcha_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verifikasi reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $recaptchaSecret,
            'response' => $request->recaptcha_token,
        ]);

        if (!$response->json()['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan gagal'
            ], 400);
        }

        $user = auth()->user();

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 400);
        }

        // Hapus foto profil jika ada
        if ($user->profile_picture) {
            Storage::delete('public/' . $user->profile_picture);
        }

        // Hapus user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error deleting account:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus akun'
        ], 500);
    }
}
}
