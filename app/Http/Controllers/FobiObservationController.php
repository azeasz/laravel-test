<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FobiObservationController extends Controller
{

    public function showUploadForm()
    {
       // Ambil data dari database second
    $faunasSecond = DB::connection('second')->table('checklist_fauna')
    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
    ->select('faunas.nameLat as name', 'checklist_fauna.count', 'checklist_fauna.breeding')
    ->get();

// Ambil data dari database third
$faunasThird = DB::connection('third')->table('checklist_fauna')
    ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
    ->select('faunas.nameLat as name', 'checklist_fauna.count', 'checklist_fauna.breeding')
    ->get();

// Gabungkan data
$faunas = $faunasSecond->merge($faunasThird);

        // Ambil data checklist
        $checklists = DB::table('fobi_checklists')->get();

        return view('fobi_upload', compact('checklists', 'faunas'));
    }
    public function storeChecklistAndFauna(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'tujuan_pengamatan' => 'required|integer',
                'fauna_id' => 'required|integer',
                'count' => 'required|integer',
                'notes' => 'nullable|string',
                'breeding' => 'required|boolean',
                'observer' => 'nullable|string',
                'breeding_note' => 'nullable|string',
                'breeding_type_id' => 'nullable|integer',
                'completed' => 'nullable|integer',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date',
                'active' => 'nullable|integer',
                'additional_note' => 'nullable|string',
                'tgl_pengamatan' => 'nullable|date',
                'images' => 'nullable|string',
            ]);

            // Log request data
            Log::info('Request data:', $request->all());

            // Dapatkan ID pengguna yang sedang login
            $userId = Auth::id();

            // Ambil burungnesia_user_id dari tabel fobi_user
            $fobiUser = DB::table('fobi_users')->where('id', $userId)->first();
            $burungnesiaUserId = $fobiUser->burungnesia_user_id;

            DB::transaction(function () use ($request, $userId, $burungnesiaUserId) {
                // Simpan ke database utama
                $checklistId = DB::table('fobi_checklists')->insertGetId([
                    'fobi_user_id' => $userId,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'tujuan_pengamatan' => $request->tujuan_pengamatan,
                    'observer' => $request->observer,
                    'additional_note' => $request->additional_note,
                    'active' => $request->active,
                    'tgl_pengamatan' => $request->tgl_pengamatan,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'completed' => $request->completed,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('fobi_checklist_faunasv1')->insert([
                    'checklist_id' => $checklistId,
                    'fauna_id' => $request->fauna_id,
                    'count' => $request->count,
                    'notes' => $request->notes,
                    'breeding' => $request->breeding,
                    'breeding_note' => $request->breeding_note,
                    'breeding_type_id' => $request->breeding_type_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('fobi_checklist_fauna_imgs')->insert([
                    'checklist_id' => $checklistId,
                    'fauna_id' => $request->fauna_id,
                    'images' => $request->images,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Simpan ke database kedua
                $checklistIdSecond = DB::connection('second')->table('checklists')->insertGetId([
                    'user_id' => $burungnesiaUserId,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'tujuan_pengamatan' => $request->tujuan_pengamatan,
                    'observer' => $request->observer,
                    'additional_note' => $request->additional_note,
                    'active' => $request->active,
                    'tgl_pengamatan' => $request->tgl_pengamatan,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'completed' => $request->completed,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::connection('second')->table('checklist_fauna')->insert([
                    'checklist_id' => $checklistIdSecond,
                    'fauna_id' => $request->fauna_id,
                    'count' => $request->count,
                    'notes' => $request->notes,
                    'breeding' => $request->breeding,
                    'breeding_note' => $request->breeding_note,
                    'breeding_type_id' => $request->breeding_type_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return back()->with('success', 'Data berhasil diunggah ke kedua database!');
        } catch (\Exception $e) {
            // Log error
            Log::error('Error uploading data: ' . $e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat mengunggah data.');
        }
    }

public function storeFauna(Request $request)
    {
        $request->validate([
            'checklist_id' => 'required|integer',
            'fauna_id' => 'required|integer',
            'count' => 'required|string',
            'notes' => 'nullable|string',
            'breeding' => 'nullable|integer',
            'breeding_note' => 'nullable|string',
            'breeding_type_id' => 'nullable|integer',
        ]);

        DB::table('fobi_checklist_faunasv1')->insert([
            'checklist_id' => $request->checklist_id,
            'fauna_id' => $request->fauna_id,
            'count' => $request->count,
            'notes' => $request->notes,
            'breeding' => $request->breeding,
            'breeding_note' => $request->breeding_note,
            'breeding_type_id' => $request->breeding_type_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Fauna berhasil diunggah!');
    }

    public function getFaunaId(Request $request)
{
    $name = $request->input('name');
    $fauna = DB::table('faunas')
        ->where('nameId', 'like', "%{$name}%")
        ->orWhere('nameLat', 'like', "%{$name}%")
        ->first();

    return response()->json(['fauna_id' => $fauna ? $fauna->id : null]);
}


    public function storeMedia(Request $request)
    {
        // Validasi data
        $request->validate([
            'media.*' => 'required|file',
            'scientific_name.*' => 'required|string',
            'date.*' => 'required|date',
            'location.*' => 'required|string',
            'habitat.*' => 'required|string',
            'description.*' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->media as $index => $mediaFile) {
                $path = $mediaFile->store('media');

                DB::table('fobi_checklist_media')->insert([
                    'checklist_id' => $request->checklist_id, // Pastikan ini valid
                    'media_type' => $mediaFile->getMimeType(),
                    'file_path' => $path,
                    'scientific_name' => $request->scientific_name[$index],
                    'date' => $request->date[$index],
                    'location' => $request->location[$index],
                    'habitat' => $request->habitat[$index],
                    'description' => $request->description[$index],
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Media berhasil diunggah!');
    }

    public function checkUpload()
{
    // Ambil data dari database utama
    $mainData = DB::table('fobi_checklists')->get();

    // Ambil data dari database kedua
    $secondData = DB::connection('second')->table('checklists')->get();

    // Ambil data dari database ketiga
    $thirdData = DB::connection('third')->table('checklists')->get();

    return view('fobi_check_upload', compact('mainData', 'secondData', 'thirdData'));
}
}
