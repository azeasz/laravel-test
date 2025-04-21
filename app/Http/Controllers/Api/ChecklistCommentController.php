<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChecklistCommentController extends Controller
{
    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $user = JWTAuth::parseToken()->authenticate();
            $source = $this->determineSource($id);

            // Tentukan actual ID
            $actualId = null;
            if (str_starts_with($id, 'BN')) {
                $actualId = (int)substr($id, 2);
            } elseif (str_starts_with($id, 'KP')) {
                $actualId = (int)substr($id, 2);
            } else {
                $actualId = (int)$id;
            }

            // Simpan komentar dengan kolom yang sesuai
            $commentData = [
                'user_id' => $user->id,
                'comment' => $request->comment,
                'source' => $source,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Tambahkan ID ke kolom yang sesuai berdasarkan source
            if ($source === 'burungnesia') {
                $commentData['burnes_checklist_id'] = $actualId;
            } elseif ($source === 'kupunesia') {
                $commentData['kupnes_checklist_id'] = $actualId;
            } else {
                $commentData['observation_id'] = $actualId;
            }

            // Simpan komentar
            $commentId = DB::table('observation_comments')->insertGetId($commentData);

            // Ambil data komentar yang baru dibuat
            $comment = DB::table('observation_comments as oc')
                ->join('fobi_users as fu', 'oc.user_id', '=', 'fu.id')
                ->where('oc.id', $commentId)
                ->select(
                    'oc.*',
                    'fu.uname as username',
                    'fu.avatar'
                )
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            Log::error('Error in store comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan komentar'
            ], 500);
        }
    }

    public function getComments($id)
    {
        try {
            $comments = DB::table('observation_comments as oc')
                ->join('fobi_users as fu', 'oc.user_id', '=', 'fu.id')
                ->where('oc.observation_id', $id)
                ->select(
                    'oc.*',
                    'fu.uname as username',
                    'fu.avatar'
                )
                ->orderBy('oc.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getComments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil komentar'
            ], 500);
        }
    }

    public function update(Request $request, $id, $commentId)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $user = JWTAuth::parseToken()->authenticate();

            // Cek kepemilikan komentar
            $comment = DB::table('observation_comments')
                ->where('id', $commentId)
                ->where('user_id', $user->id)
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komentar tidak ditemukan atau Anda tidak memiliki akses'
                ], 403);
            }

            // Update komentar
            DB::table('observation_comments')
                ->where('id', $commentId)
                ->update([
                    'comment' => $request->comment,
                    'updated_at' => now()
                ]);

            // Ambil data komentar yang diupdate
            $updatedComment = DB::table('observation_comments as oc')
                ->join('fobi_users as fu', 'oc.user_id', '=', 'fu.id')
                ->where('oc.id', $commentId)
                ->select(
                    'oc.*',
                    'fu.uname as username',
                    'fu.avatar'
                )
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil diperbarui',
                'data' => $updatedComment
            ]);

        } catch (\Exception $e) {
            Log::error('Error in update comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui komentar'
            ], 500);
        }
    }

    public function destroy($id, $commentId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Cek kepemilikan komentar
            $comment = DB::table('observation_comments')
                ->where('id', $commentId)
                ->where('user_id', $user->id)
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komentar tidak ditemukan atau Anda tidak memiliki akses'
                ], 403);
            }

            // Hapus komentar
            DB::table('observation_comments')
                ->where('id', $commentId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in delete comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus komentar'
            ], 500);
        }
    }

    private function determineSource($id)
    {
        if (str_starts_with($id, 'KP')) return 'kupunesia';
        if (str_starts_with($id, 'BN')) return 'burungnesia';
        return 'fobi';
    }
}
