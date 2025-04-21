<?php

namespace App\Http\Controllers;

use App\Models\FobiUser;
use Illuminate\Http\Request;

class FobiUserManageController extends Controller
{
    public function index()
    {
        $users = FobiUser::all();
        return view('admin.fobiuser.index', compact('users'));
    }

    public function create()
    {
        return view('admin.fobiuser.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:20',
            'lname' => 'required|string|max:20',
            'email' => 'required|email|max:50|unique:fobi_users',
            'uname' => 'required|string|max:50|unique:fobi_users',
            'password' => 'required|string|min:8',
            'level' => 'required|in:1,2,3,4',
        ]);

        FobiUser::create($request->all());

        return redirect()->route('admin.fobiuser.index')->with('success', 'User added successfully.');
    }

    public function edit(FobiUser $user)
    {
        return view('admin.fobiuser.edit', compact('user'));
    }

    public function update(Request $request, FobiUser $user)
    {
        $request->validate([
            'fname' => 'required|string|max:20',
            'lname' => 'required|string|max:20',
            'email' => 'required|email|max:50|unique:fobi_users,email,' . $user->id,
            'uname' => 'required|string|max:50|unique:fobi_users,uname,' . $user->id,
            'level' => 'required|in:1,2,3,4',
        ]);

        $user->update($request->all());

        return redirect()->route('fobiuser.index')->with('success', 'User updated successfully.');
    }

    public function destroy(FobiUser $user)
    {
        $user->delete();

        return redirect()->route('fobiuser.index')->with('success', 'User removed successfully.');
    }
}
