<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'uploadable_id' => 'required|integer',
            'uploadable_type' => 'required|string',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('uploads');

        $upload = Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'uploadable_id' => $request->uploadable_id,
            'uploadable_type' => $request->uploadable_type,
        ]);

        return response()->json($upload, 201);
    }

    public function destroy(Upload $upload)
    {
        Storage::delete($upload->file_path);
        $upload->delete();

        return response()->json(null, 204);
    }
}
