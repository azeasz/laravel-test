<?php

namespace App\Http\Controllers;

use App\Models\Fauna;
use Illuminate\Http\Request;
use App\Models\Observation;
class FaunaController extends Controller
{
    //
    public function index()
    {
        $observation = Observation::with('suggestions', 'approvedBy', 'rejectedBy')->find(1);
        return view('faunas.index', compact('observation'));
    }
    public function gallery()
    {
        $faunas = Fauna::with(['checklists' => function($query) {
            $query->join('users', 'checklists.user_id', '=', 'users.id')
                  ->select('checklists.*', 'users.profile_picture');
        }, 'checklists.user', 'users', 'userUploads', 'translations', 'uploads', 'font', 'orderFaunas'])->get();

        $id = $faunas->id;

        return view('faunas.gallery', compact('faunas'));
    }
}
