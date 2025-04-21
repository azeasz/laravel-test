<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discussion;
use Auth;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $discussion = new Discussion();
        $discussion->title = $request->title;
        $discussion->content = $request->content;
        $discussion->user_id = Auth::id();
        $discussion->save();

        return response()->json(['success' => true, 'message' => 'Diskusi berhasil ditambahkan']);
    }

    public function destroy($id)
    {
        $discussion = Discussion::where('id', $id)->where('user_id', Auth::id())->first();
        if ($discussion) {
            $discussion->delete();
            return response()->json(['success' => true, 'message' => 'Diskusi berhasil dihapus']);
        }
        return response()->json(['success' => false, 'message' => 'Diskusi tidak ditemukan']);
    }
}
