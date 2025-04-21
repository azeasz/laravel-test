<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KupunesiaChecklistController extends Controller
{
    public function update(Request $request, $id)
    {
        try {
            // Cek apakah user bisa memodifikasi checklist
            if (!$this->canModifyChecklist(auth()->user(), $id, 'kupunesia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah checklist ini'
                ], 403);
            }

            DB::beginTransaction();

            Log::info('Kupunesia update request:', [
                'id' => $id,
                'data' => $request->all()
            ]);

            // Cek di database third
            $checklist = DB::connection('third')
                ->table('checklists')
                ->where('id', $id)
                ->first();

            if (!$checklist) {
                throw new \Exception('Checklist tidak ditemukan');
            }

            // Validate request
            $request->validate([
                'tgl_pengamatan' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'fauna' => 'array',
                'fauna.*.id' => 'required|integer'
            ]);

            // Update checklist di database third
            DB::connection('third')
                ->table('checklists')
                ->where('id', $id)
                ->update([
                    'tgl_pengamatan' => $request->tgl_pengamatan,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'additional_note' => $request->additional_note,
                    'updated_at' => now()
                ]);

            // Update fauna
            foreach ($request->fauna as $fauna) {
                DB::connection('third')
                    ->table('checklist_fauna')
                    ->where([
                        'checklist_id' => $id,
                        'fauna_id' => $fauna['id']
                    ])
                    ->update([
                        'count' => $fauna['count'] ?? 0,
                        'notes' => $fauna['notes'] ?? '',
                        'breeding' => $fauna['breeding'] ?? false,
                        'breeding_note' => $fauna['breeding_note'] ?? '',
                        'breeding_type_id' => $fauna['breeding_type_id'] ?? null,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Checklist berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Kupunesia update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDetail($id)
    {
        try {
            // Hapus 'KP' prefix jika ada
            $actualId = str_starts_with($id, 'KP') ? substr($id, 2) : $id;

            Log::info('Kupunesia detail request:', [
                'id' => $id,
                'actualId' => $actualId
            ]);

            // Cek di database third
            $checklist = DB::connection('third')
                ->table('checklists as fc')
                ->join('users as fu', 'fc.user_id', '=', 'fu.id')
                ->select([
                    DB::raw("CONCAT('KP', fc.id) as id"),
                    'fc.user_id as fobi_user_id',
                    'fc.observer',
                    'fc.latitude',
                    'fc.longitude',
                    'fc.tgl_pengamatan',
                    'fc.start_time',
                    'fc.end_time',
                    'fc.additional_note',
                    'fc.created_at',
                    'fc.updated_at',
                    'fu.uname as username'
                ])
                ->where('fc.id', $actualId)
                ->first();

            if (!$checklist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checklist tidak ditemukan'
                ], 404);
            }

            // Ambil breeding types
            $breedingTypes = DB::connection('third')
                ->table('breeding_type')
                ->select(['id', 'type as name'])
                ->whereNull('deleted_at')
                ->pluck('name', 'id');

            // Ambil fauna
            $fauna = DB::connection('third')
                ->table('checklist_fauna as fcf')
                ->select([
                    DB::raw("CONCAT('KP', fcf.checklist_id) as checklist_id"),
                    DB::raw("CONCAT('KP', fcf.fauna_id) as fauna_id"),
                    'fcf.count as jumlah',
                    'fcf.notes as catatan',
                    'fcf.breeding',
                    'fcf.breeding_note',
                    'fcf.breeding_type_id',
                    'f.nameId as nama_lokal',
                    'f.nameLat as nama_ilmiah',
                    'f.family'
                ])
                ->join('faunas as f', 'fcf.fauna_id', '=', 'f.id')
                ->where('fcf.checklist_id', $actualId)
                ->whereNull('fcf.deleted_at')
                ->get()
                ->map(function($item) use ($breedingTypes) {
                    $item->breeding_type_name = $item->breeding_type_id ?
                        $breedingTypes->get($item->breeding_type_id) : null;
                    return $item;
                });

            // Ambil images
            $images = DB::connection('third')
                ->table('checklist_fauna_imgs')
                ->select([
                    'id',
                    'images as url',
                    'checklist_id',
                    'fauna_id'
                ])
                ->where('checklist_id', $actualId)
                ->whereNull('deleted_at')
                ->get();

            // Hitung total observasi
            $totalObservations = DB::connection('third')
                ->table('checklists')
                ->where('user_id', $checklist->fobi_user_id)
                ->whereNull('deleted_at')
                ->count();

            // Tambahkan informasi canEdit ke response
            $canEdit = $this->canModifyChecklist(auth()->user(), $actualId, 'kupunesia');

            $response = [
                'success' => true,
                'data' => [
                    'checklist' => [
                        'id' => $checklist->id,
                        'username' => $checklist->username,
                        'observer' => $checklist->observer,
                        'latitude' => $checklist->latitude,
                        'longitude' => $checklist->longitude,
                        'tgl_pengamatan' => $checklist->tgl_pengamatan,
                        'start_time' => $checklist->start_time,
                        'end_time' => $checklist->end_time,
                        'additional_note' => $checklist->additional_note,
                        'total_observations' => $totalObservations,
                        'can_edit' => $canEdit
                    ],
                    'fauna' => $fauna->map(function($f) {
                        return [
                            'id' => $f->fauna_id,
                            'checklist_id' => $f->checklist_id,
                            'nama_lokal' => $f->nama_lokal,
                            'nama_ilmiah' => $f->nama_ilmiah,
                            'family' => $f->family,
                            'jumlah' => $f->jumlah,
                            'catatan' => $f->catatan,
                            'breeding' => $f->breeding,
                            'breeding_note' => $f->breeding_note,
                            'breeding_type_name' => $f->breeding_type_name
                        ];
                    }),
                    'media' => [
                        'images' => $images->map(function($img) {
                            return [
                                'id' => $img->id,
                                'url' => $img->url,
                                'checklist_id' => $img->checklist_id,
                                'fauna_id' => $img->fauna_id
                            ];
                        }),
                        'sounds' => []
                    ]
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in getDetail Kupunesia:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail checklist'
            ], 500);
        }
    }
        /**
     * Check if user can modify checklist
     */
    private function canModifyChecklist($user, $checklistId, $source)
    {
        if (!$user) return false;

        $table = $source === 'kupunesia' ? 'fobi_checklists_kupnes' : 'fobi_checklists';

        $checklist = DB::table($table)
            ->where('id', $checklistId)
            ->first();

        if (!$checklist) return false;

        return $user->id === $checklist->fobi_user_id ||
               in_array($user->level, [3, 4]);
    }

}
