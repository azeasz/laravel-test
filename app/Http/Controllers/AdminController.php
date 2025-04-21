<?php

namespace App\Http\Controllers;

use App\Models\FobiUser;
use App\Models\FobiChecklistM;
use App\Models\FobiChecklistTaxa;
use App\Models\Overseer;
use App\Models\Region;
use App\Models\Taxontest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkRole:3,4');
    }

    public function dashboard()
    {
        // 1. Statistik Dasar
        $totalOverseers = FobiUser::count();
        $totalIdentifications = DB::table('taxa_identifications')->count();
        $totalMedia = DB::table('fobi_checklist_media')->count();

        // 2. Statistik Taxa
        $totalTaxa = Taxontest::count();
        $activeTaxa = Taxontest::where('status', 'active')->count();

// 3. Statistik Checklist
$fobiChecklists = FobiChecklistM::count();
$burungnesiaChecklists = DB::connection('second')
    ->table('checklists')
    ->count();
$kupunesiaChecklists = DB::connection('third')
    ->table('checklists')
    ->count();

$totalChecklists = $fobiChecklists + $burungnesiaChecklists + $kupunesiaChecklists;

$completedChecklists = FobiChecklistM::where('completed', true)->count();
        // 4. Pertumbuhan Overseer
        $lastMonthOverseers = FobiUser::whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->count();
        $overseerGrowth = $lastMonthOverseers > 0
            ? (($totalOverseers - $lastMonthOverseers) / $lastMonthOverseers) * 100
            : 0;

        // 5. Data Grafik Aktivitas Checklist (7 hari terakhir)
        $checklistChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            // FOBI Checklist Count (from primary database)
            $fobiCount = DB::table('fobi_checklist_taxas')
                ->whereDate('created_at', $date)
                ->count();

            // Burungnesia Checklist Count (from second database)
            $burungnesiaCount = DB::connection('second')
                ->table('checklists')
                ->whereDate('created_at', $date)
                ->count();

            // Kupunesia Checklist Count (from third database)
            $kupunesiaCount = DB::connection('third')
                ->table('checklists')
                ->whereDate('created_at', $date)
                ->count();

            $checklistChartData[] = [
                'date' => now()->subDays($i)->format('d M'),
                'fobi_count' => $fobiCount,
                'burungnesia_count' => $burungnesiaCount,
                'kupunesia_count' => $kupunesiaCount
            ];
        }

        $totalFobiChecklists = DB::table('fobi_checklist_taxas')->count();

    $totalBurungnesiaChecklists = DB::connection('second')
        ->table('checklists')
        ->count();

    $totalKupunesiaChecklists = DB::connection('third')
        ->table('checklists')
        ->count();

        // 6. Data Distribusi Taxa berdasarkan kelompok
        $taxaDistribution = DB::table('fobi_checklist_taxas')
            ->select('class', DB::raw('COUNT(*) as count'))
            ->whereNotNull('class')
            ->groupBy('class')
            ->get()
            ->map(function ($taxa) {
                return [
                    'name' => $taxa->class,
                    'count' => $taxa->count
                ];
            });

        // 7. Recent Activities
        $recentActivities = collect();

        // 7.1 FOBI Checklist Activities
        $recentFobiChecklists = FobiChecklistTaxa::with(['user', 'updatedBy'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($checklist) {
                return [
                    'type' => 'checklist',
                    'source' => 'fobi',
                    'id' => $checklist->id,
                    'title' => 'Observasi baru FOBI',
                    'description' => $checklist->scientific_name,
                    'user' => $checklist->user ? "{$checklist->user->fname} {$checklist->user->lname}" : 'Unknown User',
                    'date' => $checklist->created_at,
                    'icon' => 'fa-clipboard-list',
                    'color' => 'text-primary',
                    'detail_url' => route('admin.checklist.show', $checklist->id)
                ];
            });

        // 7.2 Burungnesia Checklist Activities
        $recentBurungnesiaChecklists = DB::connection('second')
            ->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->join('users', 'checklists.user_id', '=', 'users.id')
            ->select([
                'checklists.id',
                'faunas.nameLat as scientific_name',
                'users.uname as user_name',
                'checklists.created_at'
            ])
            ->latest('checklists.created_at')
            ->take(5)
            ->get()
            ->map(function ($checklist) {
                return [
                    'type' => 'checklist',
                    'source' => 'burungnesia',
                    'id' => $checklist->id,
                    'title' => 'Observasi baru Burungnesia',
                    'description' => $checklist->scientific_name,
                    'user' => $checklist->user_name,
                    'date' => $checklist->created_at,
                    'icon' => 'fa-clipboard-list',
                    'color' => 'text-success',
                    'detail_url' => '#'
                ];
            });

        // 7.3 Kupunesia Checklist Activities
        $recentKupunesiaChecklists = DB::connection('third')
            ->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->join('users', 'checklists.user_id', '=', 'users.id')
            ->select([
                'checklists.id',
                'faunas.nameLat as scientific_name',
                'users.uname as user_name',
                'checklists.created_at'
            ])
            ->latest('checklists.created_at')
            ->take(5)
            ->get()
            ->map(function ($checklist) {
                return [
                    'type' => 'checklist',
                    'source' => 'kupunesia',
                    'id' => $checklist->id,
                    'title' => 'Observasi baru Kupunesia',
                    'description' => $checklist->scientific_name,
                    'user' => $checklist->user_name,
                    'date' => $checklist->created_at,
                    'icon' => 'fa-clipboard-list',
                    'color' => 'text-warning',
                    'detail_url' => '#'
                ];
            });

        // 7.4 Recent Overseers (FOBI Users)
        $recentOverseers = FobiUser::latest()
            ->take(5)
            ->get()
            ->map(function ($overseer) {
                return [
                    'type' => 'overseer',
                    'source' => 'fobi',
                    'id' => $overseer->id,
                    'title' => 'User baru bergabung',
                    'description' => "{$overseer->fname} {$overseer->lname}",
                    'date' => $overseer->created_at,
                    'icon' => 'fa-user',
                    'color' => 'text-success',
                    'detail_url' => route('admin.fobiuser.show', $overseer->id)
                ];
            });

        // 7.5 Recent Taxa Activities
        $recentTaxa = Taxontest::with('updatedBy')
            ->where('status', 'active')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($taxa) {
                return [
                    'type' => 'taxa',
                    'source' => 'fobi',
                    'id' => $taxa->id,
                    'title' => 'Taxa diperbarui',
                    'description' => $taxa->scientific_name,
                    'user' => $taxa->updatedBy ? "{$taxa->updatedBy->fname} {$taxa->updatedBy->lname}" : 'Unknown User',
                    'date' => $taxa->updated_at,
                    'icon' => 'fa-leaf',
                    'color' => 'text-info',
                    'detail_url' => route('admin.taxa.show', $taxa->id)
                ];
            });

        // Gabungkan semua aktivitas
        $recentActivities = $recentActivities
            ->concat($recentFobiChecklists)
            ->concat($recentBurungnesiaChecklists)
            ->concat($recentKupunesiaChecklists)
            ->concat($recentOverseers)
            ->concat($recentTaxa)
            ->sortByDesc('date')
            ->take(10);

// Top FOBI Observers
$topFobiObservers = DB::table('fobi_users')
    ->select([
        'fobi_users.id',
        'fobi_users.fname as name',
        'fobi_users.lname',
        'fobi_users.organization',
        'fobi_users.profile_picture',
        DB::raw('COUNT(DISTINCT fobi_checklist_taxas.taxa_id) as taxa_count')
    ])
    ->leftJoin('fobi_checklist_taxas', 'fobi_users.id', '=', 'fobi_checklist_taxas.user_id')
    ->whereNull('fobi_checklist_taxas.deleted_at')
    ->groupBy('fobi_users.id', 'fobi_users.fname', 'fobi_users.lname', 'fobi_users.organization', 'fobi_users.profile_picture')
    ->orderByDesc('taxa_count')
    ->take(5)
    ->get()
    ->map(function ($observer) {
        return [
            'source' => 'fobi',
            'name' => $observer->name . ' ' . $observer->lname,
            'count' => $observer->taxa_count,
            'organization' => $observer->organization ?? '-',
            'profile_picture' => $observer->profile_picture ?? 'default-avatar.png'
        ];
    });

// Top Burungnesia Observers
$topBurungnesiaObservers = DB::connection('second')
    ->table('users')
    ->select([
        'users.id',
        'users.uname as name',
        'users.organization',
        DB::raw('COUNT(DISTINCT checklist_fauna.fauna_id) as taxa_count')
    ])
    ->leftJoin('checklists', 'users.id', '=', 'checklists.user_id')
    ->leftJoin('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
    ->groupBy('users.id', 'users.uname', 'users.organization')
    ->orderByDesc('taxa_count')
    ->take(5)
    ->get()
    ->map(function ($observer) {
        return [
            'source' => 'burungnesia',
            'name' => $observer->name,
            'count' => $observer->taxa_count,
            'organization' => $observer->organization ?? '-',
        ];
    });

// Top Kupunesia Observers
$topKupunesiaObservers = DB::connection('third')
    ->table('users')
    ->select([
        'users.id',
        'users.uname as name',
        'users.organization',
        DB::raw('COUNT(DISTINCT checklist_fauna.fauna_id) as taxa_count')
    ])
    ->leftJoin('checklists', 'users.id', '=', 'checklists.user_id')
    ->leftJoin('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
    ->groupBy('users.id', 'users.uname', 'users.organization')
    ->orderByDesc('taxa_count')
    ->take(5)
    ->get()
    ->map(function ($observer) {
        return [
            'source' => 'kupunesia',
            'name' => $observer->name,
            'count' => $observer->taxa_count,
            'organization' => $observer->organization ?? '-',
        ];
    });
        // Data untuk peta distribusi taxa
        $taxaLocations = collect();

        // Data dari FOBI
        $fobiLocations = DB::table('fobi_checklist_taxas')
            ->select([
                'latitude',
                'longitude',
                'scientific_name',
                'observation_details',
                DB::raw('COUNT(*) as count'),
                DB::raw("'fobi' as source")
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('latitude', 'longitude', 'scientific_name', 'observation_details')
            ->get();

        $taxaLocations = $taxaLocations->concat($fobiLocations);

// Data dari Burungnesia
$burungnesiaLocations = DB::connection('second')
    ->table('checklists')
    ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
    ->select([
        'checklists.latitude',
        'checklists.longitude',
        'faunas.nameLat as scientific_name',
        'checklist_fauna.notes as observation_details',
        DB::raw('COUNT(*) as count'),
        DB::raw("'burungnesia' as source")
    ])
    ->whereNotNull('checklists.latitude')
    ->whereNotNull('checklists.longitude')
    ->groupBy('checklists.latitude', 'checklists.longitude', 'faunas.nameLat', 'checklist_fauna.notes')
    ->get();
        $taxaLocations = $taxaLocations->concat($burungnesiaLocations);

// Data dari Kupunesia
$kupunesiaLocations = DB::connection('third')
    ->table('checklists')
    ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
    ->select([
        'checklists.latitude',
        'checklists.longitude',
        'faunas.nameLat as scientific_name',
        'checklist_fauna.notes as observation_details',
        DB::raw('COUNT(*) as count'),
        DB::raw("'kupunesia' as source")
    ])
    ->whereNotNull('checklists.latitude')
    ->whereNotNull('checklists.longitude')
    ->groupBy('checklists.latitude', 'checklists.longitude', 'faunas.nameLat', 'checklist_fauna.notes')
    ->get();
        $taxaLocations = $taxaLocations->concat($kupunesiaLocations);

        // Return view dengan semua data
        return view('admin.dashboard', compact(
            'totalOverseers',
            'totalIdentifications',
            'totalMedia',
            'totalTaxa',
            'activeTaxa',
            'totalChecklists',
            'overseerGrowth',
            'checklistChartData',
            'taxaDistribution',
            'recentActivities',
            'taxaLocations',
            'fobiChecklists',
            'burungnesiaChecklists',
            'kupunesiaChecklists',
            'completedChecklists',
            'totalFobiChecklists',
            'totalBurungnesiaChecklists',
            'totalKupunesiaChecklists',
            'topFobiObservers',
    'topBurungnesiaObservers',
    'topKupunesiaObservers'
        ));
    }

    public function landing()
    {
        return view('admin.landing');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function showChecklist($id)
    {
        try {
            $checklist = FobiChecklistTaxa::with(['user', 'medias'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'checklist',
                    'scientific_name' => $checklist->scientific_name,
                    'class' => $checklist->class,
                    'order' => $checklist->order,
                    'family' => $checklist->family,
                    'user' => [
                        'name' => $checklist->user ? "{$checklist->user->fname} {$checklist->user->lname}" : 'Unknown User'
                    ],
                    'created_at' => $checklist->created_at->format('d M Y H:i'),
                    'status' => $checklist->status,
                    'media' => $checklist->media ? asset("storage/{$checklist->media->url}") : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function showTaxa($id)
    {
        try {
            $taxa = Taxontest::with('updatedBy')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'taxa',
                    'scientific_name' => $taxa->scientific_name,
                    'family' => $taxa->family,
                    'genus' => $taxa->genus,
                    'species' => $taxa->species,
                    'common_name' => $taxa->common_name,
                    'description' => $taxa->description,
                    'status' => $taxa->status,
                    'updated_by' => [
                        'name' => $taxa->updatedBy ? "{$taxa->updatedBy->fname} {$taxa->updatedBy->lname}" : 'Unknown User'
                    ],
                    'created_at' => $taxa->created_at ,
                    'updated_at' => $taxa->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function showFobiUser($id)
    {
        try {
            $user = FobiUser::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'type' => 'user',
                    'name' => "{$user->fname} {$user->lname}",
                    'username' => $user->uname,
                    'email' => $user->email,
                    'organization' => $user->organization,
                    'level' => ucfirst($user->level), // Capitalize role
                    'profile_picture' => $user->profile_picture ?
                        asset("storage/{$user->profile_picture}") :
                        asset('images/default-avatar.png'),
                    'created_at' => $user->created_at->format('d M Y H:i'),
                    'last_login' => $user->last_login ?
                        Carbon::parse($user->last_login)->format('d M Y H:i') :
                        'Belum pernah login',
                    'bio' => $user->bio,
                    'expertise' => $user->expertise
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

}
