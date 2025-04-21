<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FobiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FobiUserApiController extends Controller
{
    // Mendapatkan daftar semua pengguna
    public function index()
    {
        $users = FobiUser::all();
        return response()->json($users);
    }

    // Mendapatkan detail pengguna berdasarkan ID
    public function show($id)
    {
        $user = FobiUser::find($id);
        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
        }
        return response()->json($user);
    }

    // Membuat pengguna baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|max:20',
            'lname' => 'required|max:20',
            'email' => 'required|email|max:50|unique:fobi_users',
            'uname' => 'required|max:50|unique:fobi_users',
            'password' => 'required|min:6',
            'phone' => 'required|max:14',
            'organization' => 'required|max:50',
        ]);

        $user = FobiUser::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'uname' => $request->uname,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'organization' => $request->organization,
            'level' => 1,
            'is_approved' => 0,
        ]);

        return response()->json(['success' => 'Pengguna berhasil dibuat', 'user' => $user], 201);
    }

    // Memperbarui pengguna
    public function update(Request $request, $id)
    {
        $user = FobiUser::find($id);
        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'fname' => 'sometimes|required|max:20',
            'lname' => 'sometimes|required|max:20',
            'email' => 'sometimes|required|email|max:50|unique:fobi_users,email,' . $id,
            'uname' => 'sometimes|required|max:50|unique:fobi_users,uname,' . $id,
            'password' => 'sometimes|required|min:6',
            'phone' => 'sometimes|required|max:14',
            'organization' => 'sometimes|required|max:50',
        ]);

        $user->update($request->only(['fname', 'lname', 'email', 'uname', 'phone', 'organization']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['success' => 'Pengguna berhasil diperbarui', 'user' => $user]);
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $user = FobiUser::find($id);
        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
        }

        $user->delete();
        return response()->json(['success' => 'Pengguna berhasil dihapus']);
    }
}
