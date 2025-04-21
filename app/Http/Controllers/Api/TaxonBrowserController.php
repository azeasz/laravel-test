<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxonBrowserController extends Controller
{
    /**
     * Mengambil data kingdom untuk halaman utama taksa
     */
    public function getKingdoms()
    {
        try {
            // Gunakan DISTINCT untuk mencegah duplikasi kingdom
            $kingdoms = DB::table('taxas as t')
                ->where('t.kingdom', '!=', '')
                ->whereNotNull('t.kingdom')
                ->where('t.status', '=', 'active')
                ->select(
                    DB::raw('MIN(t.id) as taxa_id'),
                    't.kingdom as name',
                    't.cname_kingdom as common_name',
                    DB::raw("'kingdom' as `rank`")
                )
                ->groupBy('t.kingdom', 't.cname_kingdom')
                ->get();

            // Dapatkan statistik observasi dan media untuk setiap kingdom
            $kingdomData = [];
            
            foreach ($kingdoms as $kingdom) {
                // Cek apakah kingdom ini sudah ada di array hasil
                $isDuplicate = false;
                foreach ($kingdomData as $existingKingdom) {
                    if (strtolower($existingKingdom->name) === strtolower($kingdom->name)) {
                        $isDuplicate = true;
                        break;
                    }
                }
                
                // Lewati jika duplikat
                if ($isDuplicate) continue;
                
                // Dapatkan taxa_id yang lebih akurat untuk kingdom ini
                $kingdomRecord = DB::table('taxas')
                    ->where('kingdom', '=', $kingdom->name)
                    ->where('taxon_rank', '=', 'kingdom')  // Pastikan ini benar-benar kingdom
                    ->where('status', '=', 'active')
                    ->first();
                
                if ($kingdomRecord) {
                    $kingdom->taxa_id = $kingdomRecord->id;
                }
                
                // Hitung jumlah observasi untuk kingdom ini
                $observations = DB::table('fobi_checklist_taxas as fct')
                    ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                    ->where('t.kingdom', '=', $kingdom->name)
                    ->whereNull('fct.deleted_at')
                    ->count();
                
                $kingdom->observation_count = $observations;
                
                // Ambil media gambar representatif
                $media = DB::table('fobi_checklist_media as fcm')
                    ->join('fobi_checklist_taxas as fct', 'fcm.checklist_id', '=', 'fct.id')
                    ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                    ->where('t.kingdom', '=', $kingdom->name)
                    ->select('fcm.file_path')
                    ->orderBy(DB::raw('RAND()'))
                    ->first();
                
                $kingdom->media_url = $media ? $media->file_path : null;
                
                $kingdomData[] = $kingdom;
            }
            
            // Urutkan berdasarkan jumlah observasi
            $kingdomData = collect($kingdomData)->sortByDesc('observation_count')->values()->all();

            return response()->json([
                'success' => true,
                'data' => $kingdomData
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getKingdoms:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil data taksa teratas berdasarkan jumlah observasi
     */
    public function getTopTaxa()
    {
        try {
            // Ambil data ranking dari berbagai level taksonomi
            $levels = ['kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species'];
            $result = [];
            
            foreach ($levels as $level) {
                // Dapatkan top 3 taksa untuk setiap level
                $taxaItems = DB::table('taxas as t')
                    ->where('t.taxon_rank', '=', $level)
                    ->where('t.status', '=', 'active')
                    ->where("t.$level", '!=', '')
                    ->whereNotNull("t.$level")
                    ->select(
                        't.id as taxa_id',
                        't.taxon_rank as rank',
                        "t.$level as name",
                        "t.cname_$level as common_name"
                    )
                    ->distinct("t.$level")
                    ->limit(10)
                    ->get();
                
                // Untuk setiap taksa, hitung jumlah observasi dan tambahkan media
                foreach ($taxaItems as $taxon) {
                    // Hitung observasi
                    $observationCount = DB::table('fobi_checklist_taxas as fct')
                        ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                        ->where("t.$level", '=', $taxon->name)
                        ->whereNull('fct.deleted_at')
                        ->count();
                    
                    $taxon->observation_count = $observationCount;
                    
                    // Hanya tambahkan jika memiliki observasi
                    if ($observationCount > 0) {
                        // Ambil media representatif
                        $media = DB::table('fobi_checklist_media as fcm')
                            ->join('fobi_checklist_taxas as fct', 'fcm.checklist_id', '=', 'fct.id')
                            ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                            ->where("t.$level", '=', $taxon->name)
                            ->select('fcm.file_path')
                            ->orderBy(DB::raw('RAND()'))
                            ->first();
                        
                        $taxon->media_url = $media ? $media->file_path : null;
                        
                        $result[] = $taxon;
                    }
                }
            }
            
            // Urutkan semua hasil berdasarkan jumlah observasi dan batasi hingga 12
            $topTaxa = collect($result)
                ->sortByDesc('observation_count')
                ->take(12)
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => $topTaxa
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTopTaxa:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil detail taksa berdasarkan ID
     */
    public function getTaxonDetail($taxaId)
    {
        try {
            // Ambil data taksa
            $taxon = DB::table('taxas as t')
                ->where('t.id', '=', $taxaId)
                ->select(
                    't.id as taxa_id',
                    't.scientific_name',
                    't.taxon_rank as rank',
                    't.kingdom',
                    't.phylum',
                    't.class',
                    't.order',
                    't.family',
                    't.genus',
                    't.species',
                    't.subspecies',
                    't.variety',
                    't.form',
                    't.cname_kingdom',
                    't.cname_phylum',
                    't.cname_class',
                    't.cname_order',
                    't.cname_family',
                    't.cname_genus',
                    't.cname_species',
                    't.cname_subspecies',
                    't.cname_variety',
                    't.description'
                )
                ->first();

            if (!$taxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }

            // Ekstrak nama sesuai dengan tingkat taksonomi
            switch ($taxon->rank) {
                case 'kingdom':
                    $taxon->name = $taxon->kingdom;
                    $taxon->common_name = $taxon->cname_kingdom;
                    break;
                case 'phylum':
                    $taxon->name = $taxon->phylum;
                    $taxon->common_name = $taxon->cname_phylum;
                    break;
                case 'class':
                    $taxon->name = $taxon->class;
                    $taxon->common_name = $taxon->cname_class;
                    break;
                case 'order':
                    $taxon->name = $taxon->order;
                    $taxon->common_name = $taxon->cname_order;
                    break;
                case 'family':
                    $taxon->name = $taxon->family;
                    $taxon->common_name = $taxon->cname_family;
                    break;
                case 'genus':
                    $taxon->name = $taxon->genus;
                    $taxon->common_name = $taxon->cname_genus;
                    break;
                case 'species':
                    $taxon->name = $taxon->species;
                    $taxon->common_name = $taxon->cname_species;
                    break;
                case 'subspecies':
                    $taxon->name = $taxon->subspecies;
                    $taxon->common_name = $taxon->cname_subspecies;
                    break;
                case 'variety':
                    $taxon->name = $taxon->variety;
                    $taxon->common_name = $taxon->cname_variety;
                    break;
                case 'form':
                    $taxon->name = $taxon->form;
                    $taxon->common_name = null;
                    break;
                default:
                    $taxon->name = $taxon->scientific_name;
                    $taxon->common_name = null;
            }
            
            // Hitung jumlah observasi dan tambahkan media representatif
            // Buat query observasi berdasarkan level taksonomi
            $observationQuery = DB::table('fobi_checklist_taxas as fct')
                ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                ->where("t.{$taxon->rank}", '=', $taxon->name)
                ->whereNull('fct.deleted_at');
            
            // Tambahkan semua filter taksonomi di atas level ini jika tersedia
            $taxonomyLevels = [
                'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
            ];
            
            $currentLevelIndex = array_search($taxon->rank, $taxonomyLevels);
            if ($currentLevelIndex !== false) {
                for ($i = 0; $i < $currentLevelIndex; $i++) {
                    $level = $taxonomyLevels[$i];
                    if (!empty($taxon->$level)) {
                        $observationQuery->where("t.$level", '=', $taxon->$level);
                    }
                }
            }
            
            $taxon->observation_count = $observationQuery->count();
            
            // Ambil media gambar representatif
            $media = DB::table('fobi_checklist_media as fcm')
                ->join('fobi_checklist_taxas as fct', 'fcm.checklist_id', '=', 'fct.id')
                ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                ->where("t.{$taxon->rank}", '=', $taxon->name)
                ->select('fcm.file_path')
                ->orderBy(DB::raw('RAND()'))
                ->first();
            
            $taxon->media_url = $media ? $media->file_path : null;

            // Buat breadcrumbs (taksa di level atas)
            $breadcrumbs = [];
            
            // Tentukan posisi level saat ini
            $currentLevelIndex = array_search($taxon->rank, $taxonomyLevels);
            if ($currentLevelIndex === false) {
                $currentLevelIndex = count($taxonomyLevels) - 1;
            }
            
            // Tambahkan breadcrumbs untuk level di atasnya
            for ($i = 0; $i < $currentLevelIndex; $i++) {
                $level = $taxonomyLevels[$i];
                if (!empty($taxon->$level)) {
                    $commonNameField = "cname_$level";
                    
                    // Cari taxa_id untuk level ini
                    $taxaIdForLevel = DB::table('taxas')
                        ->where($level, '=', $taxon->$level)
                        ->where('taxon_rank', '=', $level)
                        ->where('status', '=', 'active')
                        ->value('id');
                    
                    $breadcrumbs[] = [
                        'taxa_id' => $taxaIdForLevel,
                        'name' => $taxon->$level,
                        'common_name' => property_exists($taxon, $commonNameField) ? $taxon->$commonNameField : null,
                        'rank' => $level
                    ];
                }
            }
            
            // Tentukan level yang ada di bawah taksa ini
            $childLevel = null;
            if ($currentLevelIndex < count($taxonomyLevels) - 1) {
                $childLevel = $taxonomyLevels[$currentLevelIndex + 1];
            }
            
            // Ambil taksa anak jika level anak tersedia
            $children = [];
            if ($childLevel) {
                // Buat query untuk mendapatkan semua taksa unik di level anak
                $childrenRawQuery = DB::table('taxas as t')
                    ->where("t.$childLevel", '!=', '')
                    ->whereNotNull("t.$childLevel")
                    ->where('t.status', '=', 'active')
                    ->select("t.$childLevel", "t.cname_$childLevel");
                
                // Tambahkan filter untuk semua level di atas termasuk level saat ini
                for ($i = 0; $i <= $currentLevelIndex; $i++) {
                    $level = $taxonomyLevels[$i];
                    if (!empty($taxon->$level)) {
                        $childrenRawQuery->where("t.$level", '=', $taxon->$level);
                    }
                }
                
                // Dapatkan anak-taksa unik
                $rawChildren = $childrenRawQuery
                    ->groupBy("t.$childLevel", "t.cname_$childLevel")
                    ->get();
                
                // Hapus duplikasi case insensitive
                $processedChildNames = [];
                
                foreach ($rawChildren as $childRaw) {
                    $childName = $childRaw->$childLevel;
                    $childLowerName = strtolower($childName);
                    
                    // Lewati jika nama sudah ada
                    if (in_array($childLowerName, $processedChildNames)) {
                        continue;
                    }
                    
                    $processedChildNames[] = $childLowerName;
                    
                    // Dapatkan ID yang paling cocok untuk anak taksa ini
                    $childRecord = DB::table('taxas')
                        ->where($childLevel, '=', $childName)
                        ->where('taxon_rank', '=', $childLevel)
                        ->where('status', '=', 'active')
                        ->first();
                    
                    $childTaxaId = $childRecord ? $childRecord->id : null;
                    
                    if ($childTaxaId) {
                        $childTaxon = (object)[
                            'taxa_id' => $childTaxaId,
                            'name' => $childName,
                            'common_name' => $childRaw->{"cname_$childLevel"},
                            'rank' => $childLevel
                        ];
                        
                        // Hitung jumlah observasi untuk taksa anak ini
                        $childObservationsQuery = DB::table('fobi_checklist_taxas as fct')
                            ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                            ->where("t.$childLevel", '=', $childName)
                            ->whereNull('fct.deleted_at');
                        
                        // Tambahkan semua filter untuk level di atas
                        for ($i = 0; $i <= $currentLevelIndex; $i++) {
                            $level = $taxonomyLevels[$i];
                            if (!empty($taxon->$level)) {
                                $childObservationsQuery->where("t.$level", '=', $taxon->$level);
                            }
                        }
                        
                        $childTaxon->observation_count = $childObservationsQuery->count();
                        
                        // Hanya tampilkan taksa dengan observasi
                        if ($childTaxon->observation_count > 0) {
                            // Ambil media representatif
                            $childMediaQuery = DB::table('fobi_checklist_media as fcm')
                                ->join('fobi_checklist_taxas as fct', 'fcm.checklist_id', '=', 'fct.id')
                                ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                                ->where("t.$childLevel", '=', $childName);
                            
                            // Tambahkan filter untuk level di atas
                            for ($i = 0; $i <= $currentLevelIndex; $i++) {
                                $level = $taxonomyLevels[$i];
                                if (!empty($taxon->$level)) {
                                    $childMediaQuery->where("t.$level", '=', $taxon->$level);
                                }
                            }
                            
                            $childMedia = $childMediaQuery
                                ->select('fcm.file_path')
                                ->orderBy(DB::raw('RAND()'))
                                ->first();
                            
                            $childTaxon->media_url = $childMedia ? $childMedia->file_path : null;
                            
                            $children[] = $childTaxon;
                        }
                    }
                }
                
                // Urutkan berdasarkan jumlah observasi
                usort($children, function($a, $b) {
                    return $b->observation_count - $a->observation_count;
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'taxon' => $taxon,
                    'breadcrumbs' => $breadcrumbs,
                    'children' => $children
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaxonDetail:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Mengambil distribusi lokasi pengamatan untuk suatu taksa
     */
    public function getTaxonDistribution($taxaId)
    {
        try {
            // Ambil taksa untuk menentukan tingkat taksonomi
            $taxon = DB::table('taxas')
                ->where('id', '=', $taxaId)
                ->select('id', 'taxon_rank', 'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form')
                ->first();
            
            if (!$taxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }
            
            // Ekstrak nama berdasarkan tingkat taksonomi
            $taxonName = null;
            $taxonLevel = $taxon->taxon_rank;
            
            if (!empty($taxon->$taxonLevel)) {
                $taxonName = $taxon->$taxonLevel;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi taksa tidak lengkap'
                ], 404);
            }
            
            // Buat query dasar
            $query = DB::table('fobi_checklist_taxas as fct')
                ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                ->where("t.$taxonLevel", '=', $taxonName)
                ->whereNotNull('fct.latitude')
                ->whereNotNull('fct.longitude')
                ->whereNull('fct.deleted_at');
            
            // Tambahkan filter untuk level di atas taksa ini jika ada
            $taxonomyLevels = [
                'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
            ];
            
            $currentLevelIndex = array_search($taxonLevel, $taxonomyLevels);
            if ($currentLevelIndex !== false) {
                for ($i = 0; $i < $currentLevelIndex; $i++) {
                    $level = $taxonomyLevels[$i];
                    if (!empty($taxon->$level)) {
                        $query->where("t.$level", '=', $taxon->$level);
                    }
                }
            }
            
            // Ambil lokasi observasi
            $locations = $query->select('fct.latitude', 'fct.longitude', 'fct.observation_date')
                ->orderBy('fct.observation_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaxonDistribution:', [
                'taxa_id' => $taxaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari taksa berdasarkan kata kunci
     */
    public function searchTaxa(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            // Cari taksa yang sesuai dengan keyword
            $taxaMatches = DB::table('taxas as t')
                ->where('t.status', '=', 'active')
                ->where(function($q) use ($query) {
                    $q->where('t.scientific_name', 'like', "%{$query}%")
                      ->orWhere('t.kingdom', 'like', "%{$query}%")
                      ->orWhere('t.phylum', 'like', "%{$query}%")
                      ->orWhere('t.class', 'like', "%{$query}%")
                      ->orWhere('t.order', 'like', "%{$query}%")
                      ->orWhere('t.family', 'like', "%{$query}%")
                      ->orWhere('t.genus', 'like', "%{$query}%")
                      ->orWhere('t.species', 'like', "%{$query}%")
                      ->orWhere('t.cname_kingdom', 'like', "%{$query}%")
                      ->orWhere('t.cname_phylum', 'like', "%{$query}%")
                      ->orWhere('t.cname_class', 'like', "%{$query}%")
                      ->orWhere('t.cname_order', 'like', "%{$query}%")
                      ->orWhere('t.cname_family', 'like', "%{$query}%")
                      ->orWhere('t.cname_genus', 'like', "%{$query}%")
                      ->orWhere('t.cname_species', 'like', "%{$query}%");
                })
                ->select(
                    't.id as taxa_id',
                    't.taxon_rank as rank'
                )
                ->limit(30)
                ->get();
            
            // Persiapkan hasil akhir
            $results = [];
            
            // Untuk setiap taksa yang ditemukan, ambil informasi detail
            foreach ($taxaMatches as $taxaMatch) {
                // Ambil detail taksa
                $taxon = DB::table('taxas as t')
                    ->where('t.id', '=', $taxaMatch->taxa_id)
                    ->select(
                        't.id as taxa_id',
                        't.scientific_name',
                        't.taxon_rank as rank',
                        't.kingdom',
                        't.phylum',
                        't.class',
                        't.order',
                        't.family',
                        't.genus',
                        't.species',
                        't.subspecies',
                        't.variety',
                        't.form',
                        't.cname_kingdom',
                        't.cname_phylum',
                        't.cname_class',
                        't.cname_order',
                        't.cname_family',
                        't.cname_genus',
                        't.cname_species',
                        't.cname_subspecies',
                        't.cname_variety'
                    )
                    ->first();
                
                if ($taxon) {
                    // Tentukan nama dan nama umum berdasarkan rank
                    switch ($taxon->rank) {
                        case 'kingdom':
                            $taxon->name = $taxon->kingdom;
                            $taxon->common_name = $taxon->cname_kingdom;
                            break;
                        case 'phylum':
                            $taxon->name = $taxon->phylum;
                            $taxon->common_name = $taxon->cname_phylum;
                            break;
                        case 'class':
                            $taxon->name = $taxon->class;
                            $taxon->common_name = $taxon->cname_class;
                            break;
                        case 'order':
                            $taxon->name = $taxon->order;
                            $taxon->common_name = $taxon->cname_order;
                            break;
                        case 'family':
                            $taxon->name = $taxon->family;
                            $taxon->common_name = $taxon->cname_family;
                            break;
                        case 'genus':
                            $taxon->name = $taxon->genus;
                            $taxon->common_name = $taxon->cname_genus;
                            break;
                        case 'species':
                            $taxon->name = $taxon->species;
                            $taxon->common_name = $taxon->cname_species;
                            break;
                        case 'subspecies':
                            $taxon->name = $taxon->subspecies;
                            $taxon->common_name = $taxon->cname_subspecies;
                            break;
                        case 'variety':
                            $taxon->name = $taxon->variety;
                            $taxon->common_name = $taxon->cname_variety;
                            break;
                        case 'form':
                            $taxon->name = $taxon->form;
                            $taxon->common_name = null;
                            break;
                        default:
                            $taxon->name = $taxon->scientific_name;
                            $taxon->common_name = null;
                    }
                    
                    // Hitung jumlah observasi
                    $observationCount = DB::table('fobi_checklist_taxas as fct')
                        ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                        ->where("t.{$taxon->rank}", '=', $taxon->name)
                        ->whereNull('fct.deleted_at')
                        ->count();
                    
                    $taxon->observation_count = $observationCount;
                    
                    $results[] = $taxon;
                }
            }
            
            // Urutkan berdasarkan jumlah observasi
            usort($results, function($a, $b) {
                return $b->observation_count - $a->observation_count;
            });
            
            // Ambil hanya 20 hasil teratas
            $results = array_slice($results, 0, 20);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Error in searchTaxa:', [
                'query' => $request->input('q'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil semua taksa berdasarkan level taksonomi
     * contoh: /taxa/level/phylum?parent=kingdom&parent_name=Animalia
     */
    public function getTaxaByLevel(Request $request, $level)
    {
        try {
            // Validasi level taksonomi
            $validLevels = [
                'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
            ];
            
            if (!in_array($level, $validLevels)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level taksonomi tidak valid'
                ], 400);
            }
            
            // Dapatkan parameter parent level dan parent name dari request
            $parentLevel = $request->input('parent', null);
            $parentName = $request->input('parent_name', null);
            
            // Pastikan level parent ada di atas level yang diminta
            if ($parentLevel) {
                $parentIndex = array_search($parentLevel, $validLevels);
                $levelIndex = array_search($level, $validLevels);
                
                if ($parentIndex === false || $levelIndex === false || $parentIndex >= $levelIndex) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Level parent harus berada di atas level yang diminta'
                    ], 400);
                }
            }
            
            // Buat query dasar untuk mengambil taksa unik pada level ini
            $query = DB::table('taxas as t')
                ->where('t.status', '=', 'active')
                ->where("t.$level", '!=', '')
                ->whereNotNull("t.$level");
                
            // Tambahkan filter berdasarkan parent level dan name jika ada
            if ($parentLevel && $parentName) {
                $query->where("t.$parentLevel", '=', $parentName);
            }
            
            // Pilih data yang relevan dan pastikan tidak ada duplikasi
            $taxaList = $query->select(
                    DB::raw('MIN(t.id) as taxa_id'),
                    "t.$level as name",
                    DB::raw("t.cname_$level as common_name"),
                    DB::raw("'$level' as rank")
                )
                ->groupBy("t.$level", "t.cname_$level")
                ->get();
            
            // Filter untuk menghapus duplikasi lebih lanjut (case-insensitive)
            $uniqueTaxa = [];
            $processedNames = [];
            
            foreach ($taxaList as $taxon) {
                $lowerName = strtolower($taxon->name);
                if (!in_array($lowerName, $processedNames)) {
                    $processedNames[] = $lowerName;
                    
                    // Dapatkan ID yang lebih akurat
                    $betterRecord = DB::table('taxas')
                        ->where($level, '=', $taxon->name)
                        ->where('taxon_rank', '=', $level)
                        ->where('status', '=', 'active')
                        ->first();
                    
                    if ($betterRecord) {
                        $taxon->taxa_id = $betterRecord->id;
                    }
                    
                    // Hitung jumlah observasi
                    $observationQuery = DB::table('fobi_checklist_taxas as fct')
                        ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                        ->where("t.$level", '=', $taxon->name)
                        ->whereNull('fct.deleted_at');
                    
                    // Tambahkan filter parent jika diperlukan
                    if ($parentLevel && $parentName) {
                        $observationQuery->where("t.$parentLevel", '=', $parentName);
                    }
                    
                    $taxon->observation_count = $observationQuery->count();
                    
                    // Ambil media representatif
                    $mediaQuery = DB::table('fobi_checklist_media as fcm')
                        ->join('fobi_checklist_taxas as fct', 'fcm.checklist_id', '=', 'fct.id')
                        ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                        ->where("t.$level", '=', $taxon->name)
                        ->select('fcm.file_path');
                    
                    // Tambahkan filter parent jika diperlukan
                    if ($parentLevel && $parentName) {
                        $mediaQuery->where("t.$parentLevel", '=', $parentName);
                    }
                    
                    $media = $mediaQuery->orderBy(DB::raw('RAND()'))
                        ->first();
                    
                    $taxon->media_url = $media ? $media->file_path : null;
                    
                    // Tambahkan ke hasil
                    $uniqueTaxa[] = $taxon;
                }
            }
            
            // Urutkan berdasarkan jumlah observasi
            $sortedTaxa = collect($uniqueTaxa)->sortByDesc('observation_count')->values();
            
            // Tambahkan pagination
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            
            $offset = ($page - 1) * $perPage;
            $total = $sortedTaxa->count();
            $lastPage = ceil($total / $perPage);
            
            $items = $sortedTaxa->slice($offset, $perPage)->values();
            
            // Format data untuk response dengan pagination info
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $items,
                    'current_page' => (int)$page,
                    'last_page' => $lastPage,
                    'per_page' => (int)$perPage,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaxaByLevel:', [
                'level' => $level,
                'params' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik taksonomi
     */
    public function getTaxonomyStats()
    {
        try {
            // Daftar level taksonomi
            $taxonomyLevels = [
                'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
            ];
            
            $stats = [];
            
            // Hitung jumlah untuk setiap level taksonomi
            foreach ($taxonomyLevels as $level) {
                $count = DB::table('taxas')
                    ->where("$level", '!=', '')
                    ->whereNotNull("$level")
                    ->where('status', 'active')
                    ->selectRaw("COUNT(DISTINCT $level) as total")
                    ->first();
                
                $stats[$level] = $count ? $count->total : 0;
            }
            
            // Hitung jumlah observasi
            $observationCount = DB::table('fobi_checklist_taxas')
                ->whereNull('deleted_at')
                ->count();
            
            $stats['observations'] = $observationCount;
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaxonomyStats:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan relasi di antara dua taksa
     */
    public function getTaxaRelationship($firstTaxaId, $secondTaxaId)
    {
        try {
            // Dapatkan data kedua taksa
            $firstTaxa = DB::table('taxas')
                ->where('id', $firstTaxaId)
                ->select('id', 'taxon_rank', 'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form')
                ->first();
                
            $secondTaxa = DB::table('taxas')
                ->where('id', $secondTaxaId)
                ->select('id', 'taxon_rank', 'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form')
                ->first();
            
            if (!$firstTaxa || !$secondTaxa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Satu atau kedua taksa tidak ditemukan'
                ], 404);
            }
            
            // Daftar level taksonomi
            $taxonomyLevels = [
                'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
            ];
            
            // Cari level terendah yang masih sama di antara kedua taksa
            $commonLevel = null;
            $commonName = null;
            
            foreach ($taxonomyLevels as $level) {
                if (!empty($firstTaxa->$level) && !empty($secondTaxa->$level) && $firstTaxa->$level === $secondTaxa->$level) {
                    $commonLevel = $level;
                    $commonName = $firstTaxa->$level;
                } else {
                    break; // Berhenti jika menemukan perbedaan
                }
            }
            
            // Buat respons
            $relationship = [
                'common_level' => $commonLevel,
                'common_name' => $commonName,
                'first_taxa' => [
                    'id' => $firstTaxa->id,
                    'rank' => $firstTaxa->taxon_rank,
                    'taxonomic_path' => $this->buildTaxonomicPath($firstTaxa)
                ],
                'second_taxa' => [
                    'id' => $secondTaxa->id,
                    'rank' => $secondTaxa->taxon_rank,
                    'taxonomic_path' => $this->buildTaxonomicPath($secondTaxa)
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $relationship
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTaxaRelationship:', [
                'first_taxa_id' => $firstTaxaId,
                'second_taxa_id' => $secondTaxaId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function untuk membangun path taksonomi
     */
    private function buildTaxonomicPath($taxon)
    {
        $path = [];
        $taxonomyLevels = [
            'kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'
        ];
        
        foreach ($taxonomyLevels as $level) {
            if (!empty($taxon->$level)) {
                $path[$level] = $taxon->$level;
            }
        }
        
        return $path;
    }

    /**
     * Mendapatkan semua level taksonomi yang tersedia
     */
    public function getAllTaxonomicLevels()
    {
        try {
            // Daftar level taksonomi
            $taxonomyLevels = [
                ['id' => 'kingdom', 'name' => 'Kingdom', 'description' => 'Level taksonomi tertinggi'],
                ['id' => 'phylum', 'name' => 'Phylum', 'description' => 'Cabang utama dari kingdom'],
                ['id' => 'class', 'name' => 'Class', 'description' => 'Kelompok dari phylum'],
                ['id' => 'order', 'name' => 'Order', 'description' => 'Kelompok dari class'],
                ['id' => 'family', 'name' => 'Family', 'description' => 'Kelompok dari order'],
                ['id' => 'genus', 'name' => 'Genus', 'description' => 'Kelompok dari family'],
                ['id' => 'species', 'name' => 'Species', 'description' => 'Kelompok dari genus'],
                ['id' => 'subspecies', 'name' => 'Subspecies', 'description' => 'Varian dari species'],
                ['id' => 'variety', 'name' => 'Variety', 'description' => 'Varian khusus dari species'],
                ['id' => 'form', 'name' => 'Form', 'description' => 'Bentuk khusus dari species']
            ];
            
            // Hitung jumlah taksa untuk setiap level
            foreach ($taxonomyLevels as &$level) {
                $count = DB::table('taxas')
                    ->where($level['id'], '!=', '')
                    ->whereNotNull($level['id'])
                    ->where('status', 'active')
                    ->distinct($level['id'])
                    ->count($level['id']);
                
                $level['count'] = $count;
                
                // Ambil contoh taksa dari level ini
                $examples = DB::table('taxas')
                    ->where($level['id'], '!=', '')
                    ->whereNotNull($level['id'])
                    ->where('status', 'active')
                    ->select('id', $level['id'] . ' as name')
                    ->distinct($level['id'])
                    ->limit(5)
                    ->get();
                
                $level['examples'] = $examples;
            }
            
            return response()->json([
                'success' => true,
                'data' => $taxonomyLevels
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllTaxonomicLevels:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 