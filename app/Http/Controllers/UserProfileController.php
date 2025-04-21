<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\ChecklistAka;
use App\Models\ChecklistKupnes;
use App\Models\OrderFauna;
use App\Models\GenusFauna;
use App\Models\TaxaAnimalia;
use App\Models\User;
use App\Models\FobiUser;
use App\Models\FavoriteTaxa;
use App\Models\Discussion;
use App\Models\Observation;
use App\Models\Fauna;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPublicProfile']);
    }

    public function index()
    {
        // Data dummy untuk contoh
        $user = auth()->user();
        $activities = [
            'observations' => 200,
            'identifications' => 100,
        ];
        $topTaxaObservations = [
            'Burung' => 200,
            'Ikan' => 100,
            'Jamur' => 34,
            'Pohon' => 34,
            'Pakis' => 5,
        ];
        $topTaxaIdentifications = [
            'Burung' => 600,
            'Ikan' => 100,
            'Jamur' => 34,
            'Pohon' => 34,
            'Pakis' => 5,
        ];

        $following = DB::table('user_follows')
        ->join('fobi_users', 'user_follows.followed_id', '=', 'fobi_users.id')
        ->where('user_follows.follower_id', $user->id)
        ->pluck('fobi_users.uname')
        ->toArray();
        $followers = DB::table('user_follows')
    ->join('fobi_users', 'user_follows.follower_id', '=', 'fobi_users.id')
    ->where('user_follows.followed_id', $user->id)
    ->pluck('fobi_users.uname')
    ->toArray();

        return view('profile.home', compact('user', 'activities', 'topTaxaObservations', 'topTaxaIdentifications', 'following', 'followers'));
    }

    public function observasi(Request $request)
    {
        $user = auth()->user();
        $checklistsAka = collect();
        $checklistsKupnes = collect();

        // Ambil data checklist dari Burungnesia jika terhubung
        if ($user->burungnesia_user_id) {
            $checklistsAka = DB::connection('second')->table('checklists')
                ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklists.user_id', $user->burungnesia_user_id)
                ->select('checklists.*', 'checklists.latitude', 'checklists.longitude', 'faunas.nameId as fauna_name', 'faunas.family as fauna_family')
                ->get();
        }

        // Ambil data checklist dari Kupunesia jika terhubung
        if ($user->kupunesia_user_id) {
            $checklistsKupnes = DB::connection('third')->table('checklists')
                ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklists.user_id', $user->kupunesia_user_id)
                ->select('checklists.*', 'checklists.latitude', 'checklists.longitude', 'faunas.nameLat as fauna_name', 'faunas.family as fauna_family')
                ->get();
        }

        $observations = $checklistsAka->merge($checklistsKupnes)->unique('id');

        $families = Fauna::select('family')->distinct()->get();
        $orderFaunas = OrderFauna::orderBy('ordo_order')->orderBy('famili_order')->get()->keyBy('famili');
        $ordos = OrderFauna::select('ordo')->distinct()->get();
        // Tambahkan kelompok family dalam order
        $families = $families->map(function ($family) use ($orderFaunas) {
            $family->ordo = $orderFaunas->get($family->family)->ordo ?? null;
            return $family;
        });
        $faunas = Fauna::all();
        // Paginasi manual
        $perPage = $request->input('per_page', 6);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $observations->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedObservations = new LengthAwarePaginator($currentItems, $observations->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('profile.observasi', compact('user', 'observations', 'orderFaunas', 'paginatedObservations', 'families', 'ordos', 'faunas', 'checklistsAka', 'checklistsKupnes'));
    }

    public function showTaksaFavorit()
    {
        $user = auth()->user();
        $favoriteTaxa = $user->favoriteTaxa->pluck('taxa')->toArray();

        // Data dummy untuk observasi terbaru
        $favoriteTaxaObservations = [
            'Burung' => [
                (object) ['image_url' => 'path/to/image1.jpg', 'scientific_name' => 'Aves sp.', 'common_name' => 'Burung 1'],
                (object) ['image_url' => 'path/to/image2.jpg', 'scientific_name' => 'Aves sp.', 'common_name' => 'Burung 2'],
                (object) ['image_url' => 'path/to/image3.jpg', 'scientific_name' => 'Aves sp.', 'common_name' => 'Burung 3'],
                (object) ['image_url' => 'path/to/image4.jpg', 'scientific_name' => 'Aves sp.', 'common_name' => 'Burung 4'],
            ],
            'Reptil' => [
                (object) ['image_url' => 'path/to/image5.jpg', 'scientific_name' => 'Reptilia sp.', 'common_name' => 'Reptil 1'],
                (object) ['image_url' => 'path/to/image6.jpg', 'scientific_name' => 'Reptilia sp.', 'common_name' => 'Reptil 2'],
                (object) ['image_url' => 'path/to/image7.jpg', 'scientific_name' => 'Reptilia sp.', 'common_name' => 'Reptil 3'],
                (object) ['image_url' => 'path/to/image8.jpg', 'scientific_name' => 'Reptilia sp.', 'common_name' => 'Reptil 4'],
            ],
            'Ikan' => [
                (object) ['image_url' => 'path/to/image9.jpg', 'scientific_name' => 'Pisces sp.', 'common_name' => 'Ikan 1'],
                (object) ['image_url' => 'path/to/image10.jpg', 'scientific_name' => 'Pisces sp.', 'common_name' => 'Ikan 2'],
                (object) ['image_url' => 'path/to/image11.jpg', 'scientific_name' => 'Pisces sp.', 'common_name' => 'Ikan 3'],
                (object) ['image_url' => 'path/to/image12.jpg', 'scientific_name' => 'Pisces sp.', 'common_name' => 'Ikan 4'],
            ],
            'Jamur' => [
                (object) ['image_url' => 'path/to/image13.jpg', 'scientific_name' => 'Fungi sp.', 'common_name' => 'Jamur 1'],
                (object) ['image_url' => 'path/to/image14.jpg', 'scientific_name' => 'Fungi sp.', 'common_name' => 'Jamur 2'],
                (object) ['image_url' => 'path/to/image15.jpg', 'scientific_name' => 'Fungi sp.', 'common_name' => 'Jamur 3'],
                (object) ['image_url' => 'path/to/image16.jpg', 'scientific_name' => 'Fungi sp.', 'common_name' => 'Jamur 4'],
            ],
            'Pohon' => [
                (object) ['image_url' => 'path/to/image17.jpg', 'scientific_name' => 'Plantae sp.', 'common_name' => 'Pohon 1'],
                (object) ['image_url' => 'path/to/image18.jpg', 'scientific_name' => 'Plantae sp.', 'common_name' => 'Pohon 2'],
                (object) ['image_url' => 'path/to/image19.jpg', 'scientific_name' => 'Plantae sp.', 'common_name' => 'Pohon 3'],
                (object) ['image_url' => 'path/to/image20.jpg', 'scientific_name' => 'Plantae sp.', 'common_name' => 'Pohon 4'],
            ],
            'Pakis' => [
                (object) ['image_url' => 'path/to/image21.jpg', 'scientific_name' => 'Bryophyta sp.', 'common_name' => 'Pakis 1'],
                (object) ['image_url' => 'path/to/image22.jpg', 'scientific_name' => 'Bryophyta sp.', 'common_name' => 'Pakis 2'],
                (object) ['image_url' => 'path/to/image23.jpg', 'scientific_name' => 'Bryophyta sp.', 'common_name' => 'Pakis 3'],
                (object) ['image_url' => 'path/to/image24.jpg', 'scientific_name' => 'Bryophyta sp.', 'common_name' => 'Pakis 4'],
            ],
            'Mamalia' => [
                (object) ['image_url' => 'path/to/image25.jpg', 'scientific_name' => 'Mammalia sp.', 'common_name' => 'Mamalia 1'],
                (object) ['image_url' => 'path/to/image26.jpg', 'scientific_name' => 'Mammalia sp.', 'common_name' => 'Mamalia 2'],
                (object) ['image_url' => 'path/to/image27.jpg', 'scientific_name' => 'Mammalia sp.', 'common_name' => 'Mamalia 3'],
                (object) ['image_url' => 'path/to/image28.jpg', 'scientific_name' => 'Mammalia sp.', 'common_name' => 'Mamalia 4'],
            ],
            'Kupu-kupu' => [
                (object) ['image_url' => 'path/to/image29.jpg', 'scientific_name' => 'Insecta sp.', 'common_name' => 'Kupu-kupu 1'],
                (object) ['image_url' => 'path/to/image30.jpg', 'scientific_name' => 'Insecta sp.', 'common_name' => 'Kupu-kupu 2'],
                (object) ['image_url' => 'path/to/image31.jpg', 'scientific_name' => 'Insecta sp.', 'common_name' => 'Kupu-kupu 3'],
                (object) ['image_url' => 'path/to/image32.jpg', 'scientific_name' => 'Insecta sp.', 'common_name' => 'Kupu-kupu 4'],
            ],
            'Amfibi' => [
                (object) ['image_url' => 'path/to/image33.jpg', 'scientific_name' => 'Amphibia sp.', 'common_name' => 'Amfibi 1'],
                (object) ['image_url' => 'path/to/image34.jpg', 'scientific_name' => 'Amphibia sp.', 'common_name' => 'Amfibi 2'],
                (object) ['image_url' => 'path/to/image35.jpg', 'scientific_name' => 'Amphibia sp.', 'common_name' => 'Amfibi 3'],
                (object) ['image_url' => 'path/to/image36.jpg', 'scientific_name' => 'Amphibia sp.', 'common_name' => 'Amfibi 4'],
            ],
            'Akuatik' => [
                (object) ['image_url' => 'path/to/image37.jpg', 'scientific_name' => 'Aquatic sp.', 'common_name' => 'Akuatik 1'],
                (object) ['image_url' => 'path/to/image38.jpg', 'scientific_name' => 'Aquatic sp.', 'common_name' => 'Akuatik 2'],
                (object) ['image_url' => 'path/to/image39.jpg', 'scientific_name' => 'Aquatic sp.', 'common_name' => 'Akuatik 3'],
                (object) ['image_url' => 'path/to/image40.jpg', 'scientific_name' => 'Aquatic sp.', 'common_name' => 'Akuatik 4'],
            ],

        ];

        return view('profile.taksa_favorit', compact('user', 'favoriteTaxa', 'favoriteTaxaObservations'));
    }

    public function storeTaksaFavorit(Request $request)
    {
        $user = auth()->user();
        $user->favoriteTaxa()->delete(); // Hapus taksa favorit sebelumnya

        $taxa = $request->input('taksa');
        if ($taxa) {
            foreach ($taxa as $taxon) {
                FavoriteTaxa::create([
                    'fobi_user_id' => $user->id,
                    'taxa' => $taxon,
                ]);
            }
        }

        return redirect()->route('profile.taksa_favorit')->with('success', 'Taksa favorit berhasil disimpan.');
    }

    public function resetTaksaFavorit()
    {
        $user = auth()->user();
        $user->favoriteTaxa()->delete(); // Hapus semua taksa favorit

        return redirect()->route('profile.taksa_favorit')->with('success', 'Taksa favorit berhasil direset.');
    }

    public function showDiskusiIdentifikasi(Request $request, $commentId = null, $suggestionId = null, $faunaId = null)
    {
        $user = auth()->user();

        // Ambil diskusi yang melibatkan checklist observasi
        $discussionsQuery = Discussion::with(['comment', 'suggestion', 'identification'])
            ->where('user_id', $user->id)
            ->orWhereHas('comment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orWhereHas('suggestion', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orWhereHas('identification', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        // Filter berdasarkan fauna_id jika ada
        if ($faunaId) {
            $discussionsQuery->whereHas('suggestion', function ($query) use ($faunaId) {
                $query->where('fauna_id', $faunaId);
            });
        }

        $discussions = $discussionsQuery->distinct()
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil data uname, latitude, longitude, dan nameLat dari tabel checklists di database kedua
        $checklistsAka = DB::connection('second')->table('checklists')
            ->join('users', 'checklists.user_id', '=', 'users.id')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->select('checklists.id', 'users.uname', 'checklists.latitude', 'checklists.longitude', 'faunas.nameLat as scientific_name', 'faunas.id as fauna_id')
            ->get()
            ->groupBy('id');

        // Ambil data uname, latitude, longitude, dan nameLat dari tabel checklists di database ketiga
        $checklistsKupnes = DB::connection('third')->table('checklists')
            ->join('users', 'checklists.user_id', '=', 'users.id')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->select('checklists.id', 'users.uname', 'checklists.latitude', 'checklists.longitude', 'faunas.nameLat as scientific_name', 'faunas.id as fauna_id')
            ->get()
            ->groupBy('id');

        // Gabungkan data uname, latitude, longitude, dan nameLat ke dalam diskusi
        $discussions->each(function ($discussion) use ($checklistsAka, $checklistsKupnes) {
            $groupedSuggestions = $discussion->suggestion->groupBy(function ($suggestion) {
                return $suggestion->checklist_id . '-' . $suggestion->user_id;
            });

            $groupedSuggestions->each(function ($suggestions) use ($checklistsAka, $checklistsKupnes) {
                $suggestions->map(function ($suggestion) use ($checklistsAka, $checklistsKupnes) {
                    $checklistAka = $checklistsAka->get($suggestion->checklist_id);
                    $checklistKupnes = $checklistsKupnes->get($suggestion->checklist_id);

                    $checklist = $checklistAka->merge($checklistKupnes);

                    $suggestion->uname = $checklist->pluck('uname')->unique()->implode(', ') ?? 'Unknown';
                    $suggestion->latitude = $checklist->pluck('latitude')->unique()->implode(', ') ?? 'Unknown';
                    $suggestion->longitude = $checklist->pluck('longitude')->unique()->implode(', ') ?? 'Unknown';

                    // Tampilkan scientific_name yang sesuai dengan fauna_id
                    $suggestion->scientific_name = $checklist->where('fauna_id', $suggestion->fauna_id)
                        ->pluck('scientific_name')
                        ->unique()
                        ->implode(', ') ?? 'Unknown';
                });
            });

            $discussion->identifications_count = $groupedSuggestions->count();
        });

        // Paginasi
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $discussions->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedDiscussions = new LengthAwarePaginator($currentItems, $discussions->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('profile.diskusi_identifikasi', compact('user', 'paginatedDiscussions'));
    }
    public function showSpesiesSaya()
    {
        $user = auth()->user();

        // Ambil data dari database kedua
        $checklistsAka = DB::connection('second')->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->where('checklists.user_id', $user->burungnesia_user_id)
            ->select('faunas.id', 'faunas.nameId', 'faunas.nameLat', 'faunas.nameEn', 'faunas.family')
            ->get();

        // Ambil data dari database ketiga
        $checklistsKupnes = DB::connection('third')->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->where('checklists.user_id', $user->kupunesia_user_id)
            ->select('faunas.id', 'faunas.nameId', 'faunas.nameLat', 'faunas.nameEn', 'faunas.family')
            ->get();

        // Gabungkan data
        $faunas = $checklistsAka->merge($checklistsKupnes)->unique('id');

        // Ambil data genus dan taxa
        $genusFaunas = GenusFauna::whereIn('fauna_id', $faunas->pluck('id'))->get();
        $families = Fauna::select('family')->distinct()->get();
        $orderFaunas = OrderFauna::orderBy('ordo_order')->orderBy('famili_order')->get()->keyBy('famili');
        $ordos = OrderFauna::select('ordo')->distinct()->get();
        // Tambahkan kelompok family dalam order
        $families = $families->map(function ($family) use ($orderFaunas) {
            $family->ordo = $orderFaunas->get($family->family)->ordo ?? null;
            return $family;
        });


        return view('profile.spesies_saya', compact('user', 'faunas', 'checklistsAka', 'checklistsKupnes', 'genusFaunas', 'orderFaunas', 'families', 'ordos'));
    }
    public function pilihObservasi()
    {
        $user = auth()->user();
        return view('profile.pilih_observasi', compact('user'));
    }

    public function unggahObservasiBurungnesia()
    {
        $user = auth()->user();
        return view('profile.unggah_observasi_burungnesia', compact('user'));
    }

    public function unggahObservasiKupunesia()
    {
        $user = auth()->user();
        return view('profile.unggah_observasi_kupunesia', compact('user'));
    }

    public function simpanObservasiBurungnesia(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'complete_checklist' => 'required',
            'habitat' => 'required|string|max:255',
            'other_observers' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'bird_name.*' => 'required|string|max:255',
            'bird_count.*' => 'required|integer',
            'bird_behavior.*' => 'nullable|string',
            'bird_notes.*' => 'nullable|string',
        ]);

        $observation = new Observation();
        $observation->user_id = auth()->id();
        $observation->location = $request->location;
        $observation->date = $request->date;
        $observation->time_start = $request->time_start;
        $observation->time_end = $request->time_end;
        $observation->complete_checklist = $request->complete_checklist;
        $observation->habitat = $request->habitat;
        $observation->other_observers = $request->other_observers;
        $observation->description = $request->description;
        $observation->type = 'burungnesia';
        $observation->save();

        foreach ($request->bird_name as $index => $birdName) {
            $observation->birds()->create([
                'name' => $birdName,
                'count' => $request->bird_count[$index],
                'behavior' => $request->bird_behavior[$index] ?? 'no',
                'notes' => $request->bird_notes[$index] ?? '',
            ]);
        }

        return redirect()->route('profile.observasi')->with('success', 'Observasi Burungnesia berhasil diunggah.');
    }

    public function simpanObservasiKupunesia(Request $request)
    {
        // Validasi data
        $request->validate([
            'location' => 'required|string',
            'date' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'activity' => 'required|string',
            'habitat' => 'required|string',
            'other_observers' => 'nullable|string',
            'description' => 'nullable|string',
            'butterfly_species' => 'required|array',
            'butterfly_species.*' => 'required|string',
            'butterfly_count' => 'required|array',
            'butterfly_count.*' => 'required|integer',
            'butterfly_notes' => 'nullable|array',
            'butterfly_notes.*' => 'nullable|string',
        ]);

        // Simpan observasi
        $observasi = new Observation();
        $observasi->location = $request->location;
        $observasi->date = $request->date;
        $observasi->time_start = $request->time_start;
        $observasi->time_end = $request->time_end;
        $observasi->activity = $request->activity;
        $observasi->habitat = $request->habitat;
        $observasi->other_observers = $request->other_observers;
        $observasi->description = $request->description;
        $observasi->save();

        // Simpan data kupu-kupu
        foreach ($request->butterfly_species as $index => $species) {
            $butterfly = new Butterfly();
            $butterfly->species = $species;
            $butterfly->count = $request->butterfly_count[$index];
            $butterfly->notes = $request->butterfly_notes[$index] ?? null;
            $butterfly->observasi_id = $observasi->id;
            $butterfly->save();
        }

        return redirect()->route('profile.observasi')->with('success', 'Observasi berhasil disimpan.');
    }
    public function unggahObservasiMedia()
    {
        $user = auth()->user();
        return view('profile.unggah_observasi_media', compact('user'));
    }

    public function simpanObservasiMedia(Request $request)
    {
        $request->validate([
            'media.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp3,wav',
            'scientific_name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'habitat' => 'required|string|max:255',
            'description' => 'required|string',
            'source' => 'required|string',
            'is_identified' => 'required|string',
        ]);

        $observation = new Observation();
        $observation->user_id = auth()->id();
        $observation->scientific_name = $request->scientific_name;
        $observation->date = $request->date;
        $observation->location = $request->location;
        $observation->habitat = $request->habitat;
        $observation->description = $request->description;
        $observation->source = $request->source;
        $observation->is_identified = $request->is_identified;
        $observation->type = 'media';
        $observation->save();

        if ($request->hasFile('media')) {
            $mediaPaths = [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('observations', 'public');
                $mediaPaths[] = $path;
            }
            $observation->media = json_encode($mediaPaths);
        }

        return redirect()->route('profile.observasi')->with('success', 'Observasi berbasis media berhasil diunggah.');
    }

    public function showPublicProfile($username)
    {
        $user = FobiUser::where('uname', $username)->firstOrFail();

        // Data dummy untuk contoh
        $activities = [
            'observations' => 200,
            'identifications' => 100,
        ];
        $topTaxaObservations = [
            'Burung' => 200,
            'Ikan' => 100,
            'Jamur' => 34,
            'Pohon' => 34,
            'Pakis' => 5,
        ];
        $topTaxaIdentifications = [
            'Burung' => 600,
            'Ikan' => 100,
            'Jamur' => 34,
            'Pohon' => 34,
            'Pakis' => 5,
        ];
        $following = DB::table('user_follows')
        ->join('fobi_users', 'user_follows.followed_id', '=', 'fobi_users.id')
        ->where('user_follows.follower_id', $user->id)
        ->pluck('fobi_users.uname')
        ->toArray();
        $followers = DB::table('user_follows')
    ->join('fobi_users', 'user_follows.follower_id', '=', 'fobi_users.id')
    ->where('user_follows.followed_id', $user->id)
    ->pluck('fobi_users.uname')
    ->toArray();
        return view('profile.public', compact('user', 'activities', 'topTaxaObservations', 'topTaxaIdentifications', 'following', 'followers'));
    }
    public function showPublicObservations(Request $request, $uname)
    {
        $user = FobiUser::where('uname', $uname)->firstOrFail();

        // Data dummy untuk contoh
        $observations = Observation::where('user_id', $user->id)->get();
        $checklistsAka = collect();
        $checklistsKupnes = collect();

        // Ambil data checklist dari Burungnesia jika terhubung
        if ($user->burungnesia_user_id) {
            $checklistsAka = DB::connection('second')->table('checklists')
                ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklists.user_id', $user->burungnesia_user_id)
                ->select('checklists.*', 'checklists.latitude', 'checklists.longitude', 'faunas.nameId as fauna_name', 'faunas.family as fauna_family')
                ->get();
        }

        // Ambil data checklist dari Kupunesia jika terhubung
        if ($user->kupunesia_user_id) {
            $checklistsKupnes = DB::connection('third')->table('checklists')
                ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->where('checklists.user_id', $user->kupunesia_user_id)
                ->select('checklists.*', 'checklists.latitude', 'checklists.longitude', 'faunas.nameLat as fauna_name', 'faunas.family as fauna_family')
                ->get();
        }

        $observations = $checklistsAka->merge($checklistsKupnes)->unique('id');

        $families = Fauna::select('family')->distinct()->get();
        $orderFaunas = OrderFauna::orderBy('ordo_order')->orderBy('famili_order')->get()->keyBy('famili');
        $ordos = OrderFauna::select('ordo')->distinct()->get();
        // Tambahkan kelompok family dalam order
        $families = $families->map(function ($family) use ($orderFaunas) {
            $family->ordo = $orderFaunas->get($family->family)->ordo ?? null;
            return $family;
        });
        $faunas = Fauna::all();
        // Paginasi manual
        $perPage = $request->input('per_page', 6);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $observations->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedObservations = new LengthAwarePaginator($currentItems, $observations->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('profile.public_observasi', compact('user', 'observations', 'paginatedObservations'));
    }
    public function toggleFollow(Request $request)
{
    $followerId = auth()->id();
    $followedId = $request->input('user_id');

    $follow = DB::table('user_follows')
        ->where('follower_id', $followerId)
        ->where('followed_id', $followedId)
        ->first();

    if ($follow) {
        // Unfollow
        DB::table('user_follows')
            ->where('follower_id', $followerId)
            ->where('followed_id', $followedId)
            ->delete();
    } else {
        // Follow
        DB::table('user_follows')->insert([
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json(['success' => true]);
}

public function followStatus($userId)
{
    $isFollowing = DB::table('user_follows')
        ->where('follower_id', auth()->id())
        ->where('followed_id', $userId)
        ->exists();

    return response()->json(['isFollowing' => $isFollowing]);
}
public function reportUser(Request $request)
{
    $reportedUserId = $request->input('user_id');
    // Logika untuk menangani laporan pengguna
    return response()->json(['success' => true]);
}
public function editProfile(Request $request)
{
    $request->validate([
        'email' => 'required|email|max:50',
        'uname' => 'required|string|max:50',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'bio' => 'nullable|string',
    ]);

    $user = auth()->user();
    $user->email = $request->email;
    $user->uname = $request->uname;
    $user->bio = $request->bio;

    if ($request->hasFile('profile_picture')) {
        $imageName = $request->file('profile_picture')->store('profile_picture', 'public');
        $user->profile_picture = $imageName;
    }

    $user->save();

    return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
}
}
