<?php

namespace App\Http\Controllers;

use App\Models\FobiUser;
use Illuminate\Http\Request;

class OverseerController extends Controller
{
    public function index()
    {
        $overseers = FobiUser::whereIn('level', [3, 4])->get();
        return view('admin.overseer.index', compact('overseers'));
    }

    public function create()
    {
        return view('admin.overseer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:20',
            'lname' => 'required|string|max:20',
            'email' => 'required|email|max:50|unique:fobi_users',
            'uname' => 'required|string|max:50|unique:fobi_users',
            'password' => 'required|string|min:8',
            'level' => 'required|in:3,4',
        ]);

        FobiUser::create($request->all());

        return redirect()->route('overseer.index')->with('success', 'Overseer added successfully.');
    }

    public function edit(FobiUser $overseer)
    {
        return view('admin.overseer.edit', compact('overseer'));
    }

    public function update(Request $request, FobiUser $overseer)
    {
        $request->validate([
            'fname' => 'required|string|max:20',
            'lname' => 'required|string|max:20',
            'email' => 'required|email|max:50|unique:fobi_users,email,' . $overseer->id,
            'uname' => 'required|string|max:50|unique:fobi_users,uname,' . $overseer->id,
            'level' => 'required|in:3,4',
        ]);

        $overseer->update($request->all());

        return redirect()->route('overseer.index')->with('success', 'Overseer updated successfully.');
    }

    public function destroy(FobiUser $overseer)
    {
        $overseer->delete();

        return redirect()->route('overseer.index')->with('success', 'Overseer removed successfully.');
    }
}
