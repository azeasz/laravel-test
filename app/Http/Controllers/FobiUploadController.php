<?php

namespace App\Http\Controllers;

use App\Models\FobiUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FobiUploadController extends Controller
{
    public function storeBurnesKupnesia(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'media_path' => 'nullable|string',
            'scientific_name' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string',
            'habitat' => 'nullable|string',
            'description' => 'nullable|string',
            'source' => 'nullable|string',
            'is_identified' => 'nullable|boolean',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'activity' => 'nullable|string',
            'other_observers' => 'nullable|string',
        ]);

        FobiUpload::create($data);

        return redirect()->back()->with('success', 'Observasi berhasil diunggah.');
    }

    public function storeMedia(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'media.*' => 'required|file',
            'scientific_name.*' => 'required|string',
            'date.*' => 'required|date',
            'location.*' => 'required|string',
            'habitat.*' => 'required|string',
            'description.*' => 'required|string',
            'source.*' => 'required|string',
            'is_identified.*' => 'required|boolean',
        ]);

        foreach ($request->file('media') as $index => $file) {
            $path = $file->store('uploads', 'public');

            $data = [
                'type' => $request->type,
                'location' => $request->location[$index],
                'date' => $request->date[$index],
                'habitat' => $request->habitat[$index],
                'description' => $request->description[$index],
                'scientific_name' => $request->scientific_name[$index],
                'media_path' => $path,
            ];

            if (strpos($file->getMimeType(), 'audio/') === 0) {
                // Logika khusus untuk audio
                if (isset($request->source[$index])) {
                    $data['source'] = $request->source[$index];
                }
                if (isset($request->is_identified[$index])) {
                    $data['is_identified'] = $request->is_identified[$index];
                }
            }

            FobiUpload::create($data);
        }

        return redirect()->back()->with('success', 'Observasi berhasil diunggah.');
    }
    public function getChecklistsWithMedia($checklistId)
       {
           $checklist = Checklist::with('fobiUploads')->find($checklistId);

           if (!$checklist) {
               return response()->json(['message' => 'Checklist not found'], 404);
           }

           $uploads = $checklist->fobiUploads->map(function ($upload) {
               return [
                   'id' => $upload->id,
                   'type' => $upload->type,
                   'location' => $upload->location,
                   'date' => $upload->date,
                   'media' => $upload->media_path ?: ($upload->source === 'burungnesia' ? asset('storage/icon/icon.png') : asset('storage/icon/kupnes.png')),
               ];
           });

           return response()->json($uploads);
       }
}
