<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use Auth;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::all();
        $observation = Observation::all();
        return view('suggestions.index', compact('suggestions', 'observation'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'comment' => 'nullable|string',
            'observation_id' => 'required|exists:observations,id',
        ]);

        Suggestion::create([
            'name' => $request->name,
            'comment' => $request->comment,
            'user_id' => auth()->id(),
            'observation_id' => $request->observation_id,
        ]);

        return redirect()->back()->with('success', 'Saran berhasil ditambahkan!');
    }
    public function destroy($id)
    {
        $suggestion = Suggestion::where('id', $id)->where('user_id', Auth::id())->first();
        if ($suggestion) {
            $suggestion->delete();
            return response()->json(['success' => true, 'message' => 'Usulan berhasil dihapus']);
        }
        return response()->json(['success' => false, 'message' => 'Usulan tidak ditemukan']);
    }
}
