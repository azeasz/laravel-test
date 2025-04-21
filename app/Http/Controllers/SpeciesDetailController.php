<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Identification;
use App\Models\Suggestion;
use App\Models\Comment;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SpeciesDetailController extends Controller
{
    public function show($checklist_id, $fauna_id, Request $request)
    {
        $burungnesiaDetail = DB::connection('second')->table('checklist_fauna')
        ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
        ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
        ->where('faunas.id', $fauna_id)
        ->where('checklists.id', $checklist_id)
        ->select('faunas.id as fauna_id', 'faunas.nameLat', 'faunas.nameId', 'checklists.latitude', 'checklists.longitude', 'checklists.id as checklist_id')
        ->first();

    $kupunesiaDetail = DB::connection('third')->table('checklist_fauna')
        ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
        ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
        ->where('faunas.id', $fauna_id)
        ->where('checklists.id', $checklist_id)
        ->select('faunas.id as fauna_id', 'faunas.nameLat', 'faunas.nameId', 'checklists.latitude', 'checklists.longitude', 'checklists.id as checklist_id')
        ->first();


        $fobiUser = DB::table('fobi_users')->where('id', Auth::id())->first();

        $burungnesiaCount = 0;
        $kupunesiaCount = 0;
        $fobiCount = 0;

        if (Auth::check()) {
            $fobiUser = DB::table('fobi_users')->where('id', Auth::id())->first();

            if ($fobiUser) {
                $burungnesiaCount = DB::connection('second')->table('checklists')
                    ->where('user_id', $fobiUser->burungnesia_user_id)
                    ->count();

                $kupunesiaCount = DB::connection('third')->table('checklists')
                    ->where('user_id', $fobiUser->kupunesia_user_id)
                    ->count();
            }

            $fobiCount = DB::table('checklists')
                ->where('user_id', Auth::id())
                ->count();
        }
        $totalObservations = $burungnesiaCount + $kupunesiaCount + $fobiCount;


        if (!$burungnesiaDetail && !$kupunesiaDetail) {
            return response()->json(['error' => 'Species not found'], 404);
        }

        $speciesDetail = $burungnesiaDetail ?: $kupunesiaDetail;
        $ratings = Rating::where('checklist_id', $speciesDetail->checklist_id)->get();
        $averageRating = $ratings->avg('rating');
        $ratingCount = $ratings->count();

        $suggestion = Suggestion::where('checklist_id', $speciesDetail->checklist_id)->latest()->first();
        $suggestions = Suggestion::where('checklist_id', $speciesDetail->checklist_id)->get();
        $comments = Comment::where('checklist_id', $speciesDetail->checklist_id)->get();
        $identification = Identification::where('checklist_id', $speciesDetail->checklist_id)->first();
        $approvedUsers = DB::table('identifications')
        ->join('fobi_users', 'identifications.user_id', '=', 'fobi_users.id')
        ->where('identifications.checklist_id', $speciesDetail->checklist_id)
        ->where('identifications.identification', 1)
        ->select('fobi_users.id', 'fobi_users.uname', 'fobi_users.profile_picture')
        ->get();

        $rejectedUsers = DB::table('identifications')
            ->join('fobi_users', 'identifications.user_id', '=', 'fobi_users.id')
            ->where('identifications.checklist_id', $speciesDetail->checklist_id)
            ->where('identifications.identification', 0)
            ->select('fobi_users.id', 'fobi_users.uname', 'fobi_users.profile_picture')
            ->get();

// Ambil uname dari database kedua
$observerUnameSecond = DB::connection('second')->table('checklists')
->join('users', 'checklists.user_id', '=', 'users.id')
->where('checklists.id', $speciesDetail->checklist_id)
->value('users.uname');

// Ambil uname dari database ketiga
$observerUnameThird = DB::connection('third')->table('checklists')
->join('users', 'checklists.user_id', '=', 'users.id')
->where('checklists.id', $speciesDetail->checklist_id)
->value('users.uname');

// Tentukan uname yang akan digunakan
$observerUname = $observerUnameSecond ?? $observerUnameThird ?? 'Observer';


        return view('species.detail', [
            'species' => $speciesDetail,
            'average_rating' => $averageRating,
            'rating_count' => $ratingCount,
            'suggestion' => $suggestion,
            'total_observations' => $totalObservations,
            'comments' => $comments,
            'identification' => $identification,
            'approved_users' => $approvedUsers,
            'rejected_users' => $rejectedUsers,
            'suggestions' => $suggestions,
            'observerUname' => $observerUname,
            'fauna_id' => $fauna_id,
        ]);
    }

    public function storeRating(Request $request)
    {
        Log::info('Menyimpan rating', ['user_id' => Auth::id(), 'data' => $request->all()]);

        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            ['checklist_id' => $request->checklist_id, 'user_id' => Auth::id()],
            ['rating' => $request->rating]
        );

        $ratings = Rating::where('checklist_id', $request->checklist_id)->get();
        $averageRating = $ratings->avg('rating');
        $ratingCount = $ratings->count();

        return response()->json([
            'success' => true,
            'average_rating' => $averageRating,
            'rating_count' => $ratingCount,
        ]);
    }

    public function storeIdentification(Request $request)
    {
        Log::info('Menyimpan identifikasi', ['user_id' => Auth::id(), 'data' => $request->all()]);

        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'identification' => 'required|boolean',
        ]);

        Identification::updateOrCreate(
            ['checklist_id' => $request->checklist_id, 'user_id' => Auth::id()],
            ['identification' => $request->identification]
        );

        return response()->json(['success' => true]);
    }
    public function cancelIdentification(Request $request)
{
    Log::info('Membatalkan identifikasi', ['user_id' => Auth::id(), 'data' => $request->all()]);

    $request->validate([
        'checklist_id' => 'required|exists:checklists,id',
    ]);

    Identification::where('checklist_id', $request->checklist_id)
        ->where('user_id', Auth::id())
        ->update(['identification' => 0]);

    return response()->json(['success' => true]);
}

public function storeSuggestion(Request $request)
{
    try {
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'fauna_id' => 'required|exists:faunas,id',
            'suggested_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $suggestion = Suggestion::create([
            'checklist_id' => $request->checklist_id,
            'fauna_id' => $request->fauna_id,
            'user_id' => Auth::id(),
            'suggested_name' => $request->suggested_name,
            'description' => $request->description,
        ]);

        // Simpan ke tabel discussions
        Discussion::create([
            'user_id' => Auth::id(),
            'checklist_id' => $request->checklist_id,
            'suggestion_id' => $suggestion->id,
            'fauna_id' => $request->fauna_id,
        ]);

        return response()->json([
            'success' => true,
            'suggested_name' => $suggestion->suggested_name,
            'description' => $suggestion->description ?? 'Deskripsi tidak tersedia',
        ]);
    } catch (\Exception $e) {
        Log::error('Error storing suggestion: ' . $e->getMessage());
        return response()->json(['error' => 'Terjadi kesalahan saat menyimpan saran.'], 500);
    }
}    public function postComment(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'checklist_id' => 'required|exists:checklists,id',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'checklist_id' => $request->checklist_id,
            'content' => $request->comment
        ]);

        return response()->json(['success' => true]);
    }

    public function getComments($checklist_id)
    {
        $comments = Comment::where('checklist_id', $checklist_id)->get();
        return response()->json($comments);
    }
    public function cancel($id)
    {
        $suggestion = Suggestion::where('id', $id)->where('user_id', Auth::id())->first();
        if ($suggestion) {
            $suggestion->update(['is_cancelled' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }
    public function delete($id)
{
    $suggestion = Suggestion::where('id', $id)->where('user_id', Auth::id())->first();
    if ($suggestion) {
        $suggestion->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 403);
}

public function edit($id)
{
    $suggestion = Suggestion::where('id', $id)->where('user_id', Auth::id())->first();
    if ($suggestion) {
        return response()->json($suggestion);
    }
    return response()->json(['error' => 'Not found'], 404);
}

public function update(Request $request, $id)
{
    $request->validate([
        'suggested_name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $suggestion = Suggestion::where('id', $id)->where('user_id', Auth::id())->first();
    if ($suggestion) {
        $suggestion->update([
            'suggested_name' => $request->suggested_name,
            'description' => $request->description,
        ]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 403);
}

public function getFaunaSuggestions(Request $request)
{
    $query = $request->input('query');


    $secondSuggestions = DB::connection('second')->table('faunas')
        ->select('nameId', 'nameLat')
        ->where('nameId', 'like', '%' . $query . '%')
        ->orWhere('nameLat', 'like', '%' . $query . '%')
        ->get();

    $thirdSuggestions = DB::connection('third')->table('faunas')
        ->select('nameId', 'nameLat')
        ->where('nameId', 'like', '%' . $query . '%')
        ->orWhere('nameLat', 'like', '%' . $query . '%')
        ->get();

    // Gabungkan hasil dari ketiga koneksi
    $faunaSuggestions = $secondSuggestions->merge($thirdSuggestions);

    return response()->json($faunaSuggestions);
}
public function toggleFollow(Request $request)
{
    $followerId = Auth::id();
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
        ->where('follower_id', Auth::id())
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
}
