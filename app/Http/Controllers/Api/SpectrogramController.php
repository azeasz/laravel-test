<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SpectrogramController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        $audioPath = $request->file('audio')->store('audio', 'public');
        $outputPath = str_replace(['.mp3', '.wav', '.ogg'], '.png', $audioPath);

        // Gunakan jalur Python dari venv
        $pythonPath = base_path('venv/Scripts/python.exe'); // Sesuaikan dengan jalur venv Anda
        $process = new Process([$pythonPath, base_path('storage/app/public/spectrogram.py'), storage_path('app/public/' . $audioPath), storage_path('app/public/' . $outputPath)]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->json(['spectrogramUrl' => asset('storage/' . $outputPath)]);
    }
}
