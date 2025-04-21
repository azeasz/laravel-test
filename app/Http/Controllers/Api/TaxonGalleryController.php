<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxonGalleryController extends Controller
{
    // Untuk mendapatkan detail taksa berdasarkan ID taksa dan tingkat (rank)
    public function getTaxonDetail($taxaId)
    {
        try {
            $taxon = DB::table('taxas as t')
                ->where('t.id', $taxaId)
                ->select(
                    't.id as taxa_id',
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
                    't.scientific_name',
                    't.taxon_rank',
                    't.cname_kingdom',
                    't.cname_phylum',
                    't.cname_class',
                    't.cname_order',
                    't.cname_family',
                    't.cname_genus',
                    't.cname_species',
                    't.description',
                    DB::raw('COALESCE(t.iucn_red_list_category, "Tidak ada data") as iucn_red_list_category'),
                    DB::raw('COALESCE(t.status_kepunahan, "Tidak ada data") as status_kepunahan')
                )
                ->first();

            if (!$taxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }

            // Ambil semua media terkait taksa ini
            $media = DB::table('fobi_checklist_taxas as fct')
                ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->where('fct.taxa_id', $taxaId)
                ->select(
                    'fcm.id',
                    'fcm.file_path',
                    'fcm.spectrogram',
                    'fcm.habitat',
                    'fcm.location',
                    'fcm.date',
                    'fcm.description as observation_notes'
                )
                ->get();

            // Ambil lokasi pengamatan
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->where('fct.taxa_id', $taxaId)
                ->whereNotNull('fcm.location')
                ->select(
                    DB::raw('DISTINCT fcm.location'),
                    'fcm.date as observation_date',
                    DB::raw('SUBSTRING_INDEX(fcm.location, ",", 1) as latitude'),
                    DB::raw('SUBSTRING_INDEX(fcm.location, ",", -1) as longitude')
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'location' => $item->location,
                        'observation_date' => $item->observation_date,
                        'latitude' => floatval(trim($item->latitude)),
                        'longitude' => floatval(trim($item->longitude))
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'taxon' => $taxon,
                    'media' => $media,
                    'locations' => $locations
                ]
            ]);
        } catch (\Exception $e) {
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

    // Untuk mendapatkan semua taksa di bawah taksa tertentu
    public function getChildTaxa($taxaId)
    {
        try {
            $parentTaxon = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$parentTaxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }

            // Mendapatkan tingkat taksa berikutnya berdasarkan hierarki
            $hierarchy = ['kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'];
            $parentRank = $parentTaxon->taxon_rank;
            $rankIndex = array_search($parentRank, $hierarchy);
            
            // Jika tingkat taksa induk adalah yang terbawah atau tidak ditemukan
            if ($rankIndex === false || $rankIndex === count($hierarchy) - 1) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $childRank = $hierarchy[$rankIndex + 1];

            // Membangun query untuk menemukan semua taksa anak
            $query = DB::table('taxas as t')
                ->where('t.taxon_rank', $childRank);

            // Menambahkan kondisi berdasarkan taksa induk
            switch ($parentRank) {
                case 'kingdom':
                    $query->where('t.kingdom', $parentTaxon->kingdom);
                    break;
                case 'phylum':
                    $query->where('t.phylum', $parentTaxon->phylum)
                          ->where('t.kingdom', $parentTaxon->kingdom);
                    break;
                case 'class':
                    $query->where('t.class', $parentTaxon->class)
                          ->where('t.phylum', $parentTaxon->phylum)
                          ->where('t.kingdom', $parentTaxon->kingdom);
                    break;
                case 'order':
                    $query->where('t.order', $parentTaxon->order)
                          ->where('t.class', $parentTaxon->class)
                          ->where('t.phylum', $parentTaxon->phylum)
                          ->where('t.kingdom', $parentTaxon->kingdom);
                    break;
                case 'family':
                    $query->where('t.family', $parentTaxon->family)
                          ->where('t.order', $parentTaxon->order)
                          ->where('t.class', $parentTaxon->class)
                          ->where('t.phylum', $parentTaxon->phylum)
                          ->where('t.kingdom', $parentTaxon->kingdom);
                    break;
                case 'genus':
                    $query->where('t.genus', $parentTaxon->genus)
                          ->where('t.family', $parentTaxon->family);
                    break;
                case 'species':
                    $query->where('t.species', $parentTaxon->species)
                          ->where('t.genus', $parentTaxon->genus);
                    break;
                case 'subspecies':
                    $query->where('t.subspecies', $parentTaxon->subspecies)
                          ->where('t.species', $parentTaxon->species);
                    break;
                case 'variety':
                    $query->where('t.variety', $parentTaxon->variety)
                          ->where('t.species', $parentTaxon->species);
                    break;
            }

            // Menambahkan informasi jumlah observasi
            $childTaxa = $query->leftJoin('fobi_checklist_taxas as fct', function($join) {
                    $join->on('fct.taxa_id', '=', 't.id')
                        ->orWhereRaw('t.burnes_fauna_id = fct.taxa_id')
                        ->orWhereRaw('t.kupnes_fauna_id = fct.taxa_id');
                })
                ->leftJoin('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->select(
                    't.id as taxa_id',
                    "t.{$childRank} as name",
                    't.scientific_name',
                    't.taxon_rank',
                    "t.cname_{$childRank} as common_name",
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    "t.{$childRank}",
                    't.scientific_name',
                    't.taxon_rank',
                    "t.cname_{$childRank}",
                    't.description'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'parent_taxon' => $parentTaxon,
                    'child_rank' => $childRank,
                    'child_taxa' => $childTaxa
                ]
            ]);
        } catch (\Exception $e) {
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

    // Untuk mendapatkan distribusi geografis dari taksa tertentu
    public function getTaxonDistribution($taxaId)
    {
        try {
            $taxon = DB::table('taxas')
                ->where('id', $taxaId)
                ->first();

            if (!$taxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }

            // Ambil lokasi pengamatan untuk taksa ini dan semua taksa di bawahnya
            $locations = DB::table('fobi_checklist_taxas as fct')
                ->join('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id')
                ->join('taxas as t', 'fct.taxa_id', '=', 't.id')
                ->where(function($query) use ($taxon) {
                    // Filter berdasarkan hierarki taksonomi
                    switch ($taxon->taxon_rank) {
                        case 'kingdom':
                            $query->where('t.kingdom', $taxon->kingdom);
                            break;
                        case 'phylum':
                            $query->where('t.phylum', $taxon->phylum);
                            break;
                        case 'class':
                            $query->where('t.class', $taxon->class);
                            break;
                        case 'order':
                            $query->where('t.order', $taxon->order);
                            break;
                        case 'family':
                            $query->where('t.family', $taxon->family);
                            break;
                        case 'genus':
                            $query->where('t.genus', $taxon->genus);
                            break;
                        case 'species':
                            $query->where('t.species', $taxon->species);
                            break;
                        case 'subspecies':
                            $query->where('t.subspecies', $taxon->subspecies);
                            break;
                        case 'variety':
                            $query->where('t.variety', $taxon->variety);
                            break;
                        case 'form':
                            $query->where('t.form', $taxon->form);
                            break;
                        default:
                            $query->where('t.id', $taxon->id);
                    }
                })
                ->whereNotNull('fcm.location')
                ->select(
                    DB::raw('DISTINCT fcm.location'),
                    'fcm.date as observation_date',
                    DB::raw('SUBSTRING_INDEX(fcm.location, ",", 1) as latitude'),
                    DB::raw('SUBSTRING_INDEX(fcm.location, ",", -1) as longitude')
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'location' => $item->location,
                        'observation_date' => $item->observation_date,
                        'latitude' => floatval(trim($item->latitude)),
                        'longitude' => floatval(trim($item->longitude))
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Untuk mendapatkan daftar kingdom
    public function getKingdoms(Request $request)
    {
        try {
            $query = DB::table('taxas as t')
                ->where('t.taxon_rank', 'kingdom')
                ->leftJoin('fobi_checklist_taxas as fct', function($join) {
                    $join->on('fct.taxa_id', '=', 't.id')
                        ->orWhereRaw('t.burnes_fauna_id = fct.taxa_id')
                        ->orWhereRaw('t.kupnes_fauna_id = fct.taxa_id');
                })
                ->leftJoin('fobi_checklist_media as fcm', 'fct.id', '=', 'fcm.checklist_id');

            // Tambahkan filter pencarian jika ada
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('t.kingdom', 'like', "%{$search}%")
                        ->orWhere('t.cname_kingdom', 'like', "%{$search}%");
                });
            }

            $kingdoms = $query->select(
                    't.id as taxa_id',
                    't.kingdom as name',
                    't.cname_kingdom as common_name',
                    't.description',
                    DB::raw('COUNT(DISTINCT fct.id) as observation_count'),
                    DB::raw('GROUP_CONCAT(DISTINCT fcm.file_path) as media_paths')
                )
                ->groupBy(
                    't.id',
                    't.kingdom',
                    't.cname_kingdom',
                    't.description'
                )
                ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $kingdoms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Untuk mendapatkan taksonomi lengkap dari ID taksa
    public function getTaxonomyPath($taxaId)
    {
        try {
            $taxon = DB::table('taxas')
                ->where('id', $taxaId)
                ->select(
                    'id',
                    'kingdom',
                    'phylum',
                    'class',
                    'order',
                    'family',
                    'genus',
                    'species',
                    'subspecies',
                    'variety',
                    'form',
                    'taxon_rank',
                    'cname_kingdom',
                    'cname_phylum',
                    'cname_class',
                    'cname_order',
                    'cname_family',
                    'cname_genus',
                    'cname_species'
                )
                ->first();

            if (!$taxon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Taksa tidak ditemukan'
                ], 404);
            }

            // Membuat array untuk menyimpan jalur taksonomi
            $taxonomyPath = [];
            $hierarchy = ['kingdom', 'phylum', 'class', 'order', 'family', 'genus', 'species', 'subspecies', 'variety', 'form'];
            
            foreach ($hierarchy as $rank) {
                if (!empty($taxon->$rank)) {
                    // Cari ID taksa untuk level ini
                    $rankTaxon = DB::table('taxas')
                        ->where('taxon_rank', $rank);
                    
                    // Tambahkan kondisi untuk setiap level taksonomi yang ada
                    $conditions = [];
                    foreach ($hierarchy as $ancestorRank) {
                        if ($ancestorRank === $rank) {
                            $conditions[$ancestorRank] = $taxon->$rank;
                            break;
                        }
                        if (!empty($taxon->$ancestorRank)) {
                            $conditions[$ancestorRank] = $taxon->$ancestorRank;
                        }
                    }
                    
                    foreach ($conditions as $condRank => $value) {
                        $rankTaxon->where($condRank, $value);
                    }
                    
                    $result = $rankTaxon->select('id as taxa_id', $rank . ' as name', 'cname_' . $rank . ' as common_name', 'taxon_rank')
                        ->first();
                    
                    if ($result) {
                        $taxonomyPath[] = $result;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $taxonomyPath
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 