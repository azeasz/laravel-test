<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderFauna;
use App\Models\ChecklistAka;
use App\Models\ChecklistKupnes;
use App\Models\Fauna;
use App\Models\Taxontest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    public function index()
    {
        $families = Fauna::select('family')->distinct()->get();
        $orderFaunas = OrderFauna::orderBy('ordo_order')->orderBy('famili_order')->get()->keyBy('famili');
        $ordos = OrderFauna::select('ordo')->distinct()->get();
        // Tambahkan kelompok family dalam order
        $families = $families->map(function ($family) use ($orderFaunas) {
            $family->ordo = $orderFaunas->get($family->family)->ordo ?? null;
            return $family;
        });

        $faunas = Fauna::all();
        $taxontest = Taxontest::all();

        $checklistsAka = DB::connection('second')->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->select('checklists.latitude', 'checklists.longitude', 'checklists.id', 'checklists.created_at', DB::raw("'burungnesia' as source"))
            ->groupBy('checklists.latitude', 'checklists.longitude', 'checklists.id', 'checklists.created_at')
            ->get();

        $checklistsKupnes = DB::connection('third')->table('checklists')
            ->join('checklist_fauna', 'checklists.id', '=', 'checklist_fauna.checklist_id')
            ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
            ->select('checklists.latitude', 'checklists.longitude', 'checklists.id', 'checklists.created_at', DB::raw("'kupunesia' as source"))
            ->groupBy('checklists.latitude', 'checklists.longitude', 'checklists.id', 'checklists.created_at')
            ->get();

        $checklists = $checklistsAka->merge($checklistsKupnes);
        $burungnesiaCount = DB::connection('second')->table('checklist_fauna')->count();
        $kupunesiaCount = DB::connection('third')->table('checklist_fauna')->count();


        $user = Auth::user();
        $userBurungnesiaCount = 0;
        $userKupunesiaCount = 0;
        $fobiCount = 0;
        if (Auth::check()) {
            $fobiUser = DB::table('fobi_users')->where('id', Auth::id())->first();

            if ($fobiUser) {
                $userBurungnesiaCount = DB::connection('second')->table('checklists')
                    ->where('user_id', $fobiUser->burungnesia_user_id)
                    ->count();

                $userKupunesiaCount = DB::connection('third')->table('checklists')
                    ->where('user_id', $fobiUser->kupunesia_user_id)
                    ->count();
            }

            $fobiCount = DB::table('checklists')
                ->where('user_id', Auth::id())
                ->count();
        }

        $userTotalObservations = $userBurungnesiaCount + $userKupunesiaCount + $fobiCount;

        return view('home', compact('orderFaunas', 'checklists', 'families', 'ordos', 'faunas', 'taxontest', 'burungnesiaCount', 'kupunesiaCount', 'userBurungnesiaCount', 'userKupunesiaCount', 'userTotalObservations'));
    }


}
