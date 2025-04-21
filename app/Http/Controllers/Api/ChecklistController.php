<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FobiChecklist;
use App\Models\FobiChecklistFauna;

class ChecklistController extends Controller
{
    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $checklist = FobiChecklist::findOrFail($id);

            // Cek authorization
            if ($user->id !== $checklist->fobi_user_id && !in_array($user->level, [3, 4])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Update checklist
            $checklist->update($request->only([
                'tgl_pengamatan',
                'start_time',
                'end_time',
                'latitude',
                'longitude',
                'additional_note'
            ]));

            // Update fauna
            foreach ($request->fauna as $faunaData) {
                if (isset($faunaData['isDeleted']) && $faunaData['isDeleted']) {
                    // Delete fauna from checklist
                    FobiChecklistFauna::where([
                        'checklist_id' => $id,
                        'fauna_id' => $faunaData['id']
                    ])->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Checklist berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui checklist'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $checklist = FobiChecklist::findOrFail($id);

            if ($user->id !== $checklist->fobi_user_id && !in_array($user->level, [3, 4])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Delete related records
            FobiChecklistFauna::where('checklist_id', $id)->delete();
            $checklist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Checklist berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus checklist'
            ], 500);
        }
    }

    /**
     * Delete specific fauna from checklist
     */
    public function deleteFauna($checklistId, $faunaId)
    {
        try {
            $checklist = FobiChecklist::findOrFail($checklistId);

            // Authorization sudah ditangani oleh middleware policy

            // Delete fauna dari checklist
            $deleted = FobiChecklistFauna::where([
                'checklist_id' => $checklistId,
                'fauna_id' => $faunaId
            ])->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Spesies berhasil dihapus dari checklist'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Spesies tidak ditemukan dalam checklist'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus spesies'
            ], 500);
        }
    }
}
