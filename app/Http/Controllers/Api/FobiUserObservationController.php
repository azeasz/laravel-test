<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FobiChecklistTaxa;
use App\Models\FobiChecklistMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FobiUserObservationController extends Controller
{
    /**
     * Mendapatkan daftar observasi milik user tertentu
     */
    public function getUserObservations(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            
            // Validasi parameter
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $searchType = $request->input('search_type', 'all');
            $dateFilter = $request->input('date', '');
            
            // Query dasar
            $query = FobiChecklistTaxa::with(['medias' => function($query) {
                $query->select([
                    'id',
                    'checklist_id',
                    'media_type',
                    'file_path',
                    'spectrogram',
                    'created_at',
                    'updated_at'
                ]);
            }])
            ->where('user_id', $userId);
                
            // Filter berdasarkan pencarian
            if (!empty($search)) {
                if ($searchType === 'species') {
                    $query->where(function($q) use ($search) {
                        $q->where('scientific_name', 'like', '%' . $search . '%')
                          ->orWhere('genus', 'like', '%' . $search . '%')
                          ->orWhere('species', 'like', '%' . $search . '%');
                    });
                } elseif ($searchType === 'location') {
                    $query->where(function($q) use ($search) {
                        $q->whereRaw("CONCAT(latitude, ', ', longitude) like ?", ['%' . $search . '%']);
                    });
                } elseif ($searchType === 'date') {
                    $query->whereDate('created_at', $search);
                } else {
                    // All search
                    $query->where(function($q) use ($search) {
                        $q->where('scientific_name', 'like', '%' . $search . '%')
                          ->orWhere('genus', 'like', '%' . $search . '%')
                          ->orWhere('species', 'like', '%' . $search . '%')
                          ->orWhereRaw("CONCAT(latitude, ', ', longitude) like ?", ['%' . $search . '%']);
                    });
                }
            }
            
            // Filter berdasarkan tanggal
            if (!empty($dateFilter)) {
                $query->whereDate('created_at', $dateFilter);
            }
            
            // Ambil data dengan paginasi
            $observations = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);
                
            // Tambahkan URL foto dan format data
            $observations->getCollection()->transform(function ($observation) {
                // Format media
                $media = [
                    'images' => [],
                    'sounds' => []
                ];

                foreach ($observation->medias as $mediaItem) {
                    $mediaUrl = url('storage/' . $mediaItem->file_path);
                    
                    if ($mediaItem->media_type === 'photo') {
                        $media['images'][] = [
                            'id' => $mediaItem->id,
                            'url' => $mediaUrl,
                            'thumbnail_url' => $mediaUrl,
                            'type' => 'image'
                        ];
                    } elseif ($mediaItem->media_type === 'audio') {
                        $media['sounds'][] = [
                            'id' => $mediaItem->id,
                            'url' => $mediaUrl,
                            'spectrogram_url' => $mediaItem->spectrogram ? url('storage/' . $mediaItem->spectrogram) : null,
                            'type' => 'audio'
                        ];
                    }
                }

                // Ambil media pertama untuk thumbnail
                $firstMedia = $observation->medias->first();
                if ($firstMedia) {
                    $observation->photo_url = url('storage/' . $firstMedia->file_path);
                    $observation->media_type = $firstMedia->media_type;
                }
                
                // Format data tambahan
                $observation->formatted_date = $observation->created_at->format('d F Y');
                $observation->location_name = $this->getLocationName($observation->latitude, $observation->longitude);
                $observation->media = $media;
                
                return $observation;
            });
            
            return response()->json([
                'success' => true,
                'data' => $observations,
                'message' => 'Daftar observasi berhasil dimuat'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar observasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan detail observasi
     */
    public function getObservationDetail($id)
    {
        try {
            $userId = auth()->user()->id;
            
            $observation = FobiChecklistTaxa::with('medias', 'user')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();
                
            if (!$observation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Observasi tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            // Format data tambahan
            $observation->formatted_date = $observation->created_at->format('d F Y');
            $observation->location_name = $this->getLocationName($observation->latitude, $observation->longitude);
            
            // Format media
            $observation->medias->transform(function ($media) {
                // Pastikan URL media bisa diakses
                $media->full_url = url('storage/' . str_replace('public/', '', $media->file_path));
                $media->thumbnail_url = url('storage/' . str_replace('public/', '', $media->file_path)); // Bisa ditambahkan logika thumbnail jika ada
                if ($media->spectrogram) {
                    $media->spectrogram = url('storage/' . str_replace('public/', '', $media->spectrogram));
                }
                return $media;
            });
            
            return response()->json([
                'success' => true,
                'data' => $observation,
                'message' => 'Detail observasi berhasil dimuat'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail observasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update data observasi
     */
    public function updateObservation(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'scientific_name' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'kingdom' => 'nullable|string|max:255',
                'phylum' => 'nullable|string|max:255',
                'class' => 'nullable|string|max:255',
                'order' => 'nullable|string|max:255',
                'family' => 'nullable|string|max:255',
                'genus' => 'nullable|string|max:255',
                'species' => 'nullable|string|max:255',
                'observation_details' => 'nullable|json',
                'observation_date' => 'nullable|date',
                'new_media' => 'nullable|array',
                'new_media.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,mp3,wav|max:20480',
                'media_to_delete' => 'nullable|array',
                'media_to_delete.*' => 'integer'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cari observasi
            $observation = FobiChecklistTaxa::where('id', $id)
                ->where('user_id', $userId)
                ->first();
                
            if (!$observation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Observasi tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            // Update data observasi
            DB::beginTransaction();
            
            $observation->scientific_name = $request->scientific_name;
            $observation->latitude = $request->latitude;
            $observation->longitude = $request->longitude;
            $observation->kingdom = $request->kingdom;
            $observation->phylum = $request->phylum;
            $observation->class = $request->class;
            $observation->order = $request->order;
            $observation->family = $request->family;
            $observation->genus = $request->genus;
            $observation->species = $request->species;
            
            if ($request->has('observation_details')) {
                $observation->observation_details = json_decode($request->observation_details, true);
            }
            
            if ($request->has('observation_date')) {
                $observation->observation_date = $request->observation_date;
            }
            
            // Simpan perubahan pada observasi
            $observation->updated_by = $userId;
            $observation->save();
            
            // Hapus media yang dipilih
            if ($request->has('media_to_delete') && is_array($request->media_to_delete)) {
                foreach ($request->media_to_delete as $mediaId) {
                    $media = FobiChecklistMedia::where('id', $mediaId)
                        ->where('checklist_id', $observation->id)
                        ->first();
                        
                    if ($media) {
                        // Hapus file fisik jika ada
                        if (Storage::exists($media->file_path)) {
                            Storage::delete($media->file_path);
                        }
                        if ($media->spectrogram && Storage::exists($media->spectrogram)) {
                            Storage::delete($media->spectrogram);
                        }
                        
                        // Hapus record dari database
                        $media->delete();
                    }
                }
            }
            
            // Proses media baru
            if ($request->hasFile('new_media')) {
                foreach ($request->file('new_media') as $file) {
                    $path = $file->store('public/observations/' . $observation->id);
                    $publicPath = Storage::url($path);
                    
                    // Deteksi tipe media
                    $extension = $file->getClientOriginalExtension();
                    $mediaType = in_array(strtolower($extension), ['mp4', 'mov', 'avi']) ? 'video' : 
                                (in_array(strtolower($extension), ['mp3', 'wav']) ? 'audio' : 'image');
                    
                    // Buat record media baru
                    $media = new FobiChecklistMedia();
                    $media->checklist_id = $observation->id;
                    $media->media_type = $mediaType;
                    $media->file_path = $publicPath;
                    $media->scientific_name = $observation->scientific_name;
                    $media->location = "Lat: {$observation->latitude}, Long: {$observation->longitude}";
                    $media->date = $observation->observation_date ?? now()->toDateString();
                    $media->save();
                    
                    // Jika audio, buat spectrogram
                    if ($mediaType === 'audio') {
                        // Logic untuk membuat spectrogram akan ditambahkan di sini
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $observation->fresh(['medias']),
                'message' => 'Observasi berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui observasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Hapus observasi
     */
    public function deleteObservation($id)
    {
        try {
            $userId = Auth::id();
            
            // Cari observasi
            $observation = FobiChecklistTaxa::where('id', $id)
                ->where('user_id', $userId)
                ->first();
                
            if (!$observation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Observasi tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }
            
            DB::beginTransaction();
            
            try {
                // Hapus notifikasi terkait
                DB::table('taxa_notifications')->where('checklist_id', $observation->id)->delete();
                \Log::info("Notifikasi terkait observasi ID {$id} berhasil dihapus");

                // Hapus identifikasi terkait
                DB::table('taxa_identifications')->where('checklist_id', $observation->id)->delete();
                \Log::info("Identifikasi terkait observasi ID {$id} berhasil dihapus");

                // Hapus verifikasi lokasi terkait
                DB::table('taxa_location_verifications')->where('checklist_id', $observation->id)->delete();
                \Log::info("Verifikasi lokasi terkait observasi ID {$id} berhasil dihapus");

                // Hapus wild status votes terkait
                DB::table('taxa_wild_status_votes')->where('checklist_id', $observation->id)->delete();
                \Log::info("Wild status votes terkait observasi ID {$id} berhasil dihapus");

                // Hapus quality assessment terkait
                DB::table('taxa_quality_assessments')->where('taxa_id', $observation->id)->delete();
                \Log::info("Quality assessment terkait observasi ID {$id} berhasil dihapus");

                // Hapus comments terkait
                DB::table('observation_comments')->where('observation_id', $observation->id)->delete();
                \Log::info("Komentar terkait observasi ID {$id} berhasil dihapus");

                // Hapus flags terkait
                DB::table('taxa_flags')->where('checklist_id', $observation->id)->delete();
                \Log::info("Flags terkait observasi ID {$id} berhasil dihapus");

                // Hapus semua media terkait
                $medias = FobiChecklistMedia::where('checklist_id', $observation->id)->get();
                foreach ($medias as $media) {
                    // Hapus file fisik jika ada
                    if (Storage::exists($media->file_path)) {
                        Storage::delete($media->file_path);
                    }
                    if ($media->spectrogram && Storage::exists($media->spectrogram)) {
                        Storage::delete($media->spectrogram);
                    }
                    
                    // Hapus record dari database
                    $media->delete();
                }
                \Log::info("Media terkait observasi ID {$id} berhasil dihapus");
                
                // Hapus observasi
                $observation->delete();
                \Log::info("Observasi ID {$id} berhasil dihapus");
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Observasi berhasil dihapus'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Gagal menghapus observasi ID {$id}: " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus observasi: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error("Error saat mengakses observasi ID {$id}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan saran pencarian
     */
    public function getSearchSuggestions(Request $request)
    {
        try {
            $userId = Auth::id();
            $query = $request->get('q', '');
            $type = $request->get('type', 'all');
            
            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $suggestions = [];
            
            // Berdasarkan tipe pencarian
            if ($type === 'species' || $type === 'all') {
                // Cari berdasarkan nama spesies
                $speciesSuggestions = FobiChecklistTaxa::where('user_id', $userId)
                    ->where(function($q) use ($query) {
                        $q->where('scientific_name', 'like', '%' . $query . '%')
                          ->orWhere('genus', 'like', '%' . $query . '%')
                          ->orWhere('species', 'like', '%' . $query . '%');
                    })
                    ->select('scientific_name', 'genus', 'species')
                    ->distinct()
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'scientific_name' => $item->scientific_name,
                            'type' => 'species'
                        ];
                    });
                    
                $suggestions = array_merge($suggestions, $speciesSuggestions->toArray());
            }
            
            if ($type === 'location' || $type === 'all') {
                // Karena lokasi disimpan sebagai koordinat, kita berikan beberapa contoh lokasi
                // Dalam implementasi nyata, Anda mungkin perlu menggunakan geocoding service
                $locationSuggestions = [
                    ['name' => 'Jakarta', 'type' => 'location'],
                    ['name' => 'Surabaya', 'type' => 'location'],
                    ['name' => 'Bandung', 'type' => 'location'],
                    ['name' => 'Bali', 'type' => 'location'],
                ];
                
                // Filter berdasarkan query
                $locationSuggestions = array_filter($locationSuggestions, function($item) use ($query) {
                    return stripos($item['name'], $query) !== false;
                });
                
                $suggestions = array_merge($suggestions, array_values($locationSuggestions));
            }
            
            // Limit hasil
            $suggestions = array_slice($suggestions, 0, 10);
            
            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan saran pencarian: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper untuk mendapatkan nama lokasi dari koordinat
     */
    private function getLocationName($latitude, $longitude)
    {
        // Dalam implementasi nyata, Anda bisa menggunakan API geocoding
        // Untuk sementara, kita gunakan format koordinat saja
        return "Lat: {$latitude}, Long: {$longitude}";
    }
} 