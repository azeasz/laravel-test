<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObservationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = $this->getBaseQuery($request->source);

            // Filter tanggal
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter media
            if ($request->has_media) {
                $query->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from($this->getMediaTable($request->source))
                        ->whereRaw('checklist_id = checklists.id');
                        
                    if ($request->media_type) {
                        $query->where('media_type', $request->media_type);
                    }
                });
            }

            // Pengurutan
            switch ($request->sort_by) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'most_identifications':
                    $query->withCount('identifications')
                        ->orderBy('identifications_count', 'desc');
                    break;
                case 'most_agreements':
                    $query->withCount('agreements')
                        ->orderBy('agreements_count', 'desc');
                    break;
                default: // newest
                    $query->orderBy('created_at', 'desc');
            }

            $observations = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $observations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data observasi'
            ], 500);
        }
    }

    private function getBaseQuery($source)
    {
        switch ($source) {
            case 'burungnesia':
                return DB::table('burnes_checklists')
                    ->join('burnes.faunas', 'burnes_checklists.fauna_id', '=', 'burnes.faunas.id')
                    ->join('fobi_users', 'burnes_checklists.user_id', '=', 'fobi_users.id');
            case 'kupunesia':
                return DB::table('kupnes_checklists')
                    ->join('kupnes.faunas', 'kupnes_checklists.fauna_id', '=', 'kupnes.faunas.id')
                    ->join('fobi_users', 'kupnes_checklists.user_id', '=', 'fobi_users.id');
            default:
                return DB::table('fobi_checklists')
                    ->join('fobi_taxa', 'fobi_checklists.taxa_id', '=', 'fobi_taxa.id')
                    ->join('fobi_users', 'fobi_checklists.user_id', '=', 'fobi_users.id');
        }
    }

    private function getMediaTable($source)
    {
        switch ($source) {
            case 'burungnesia':
                return 'burnes_checklist_media';
            case 'kupunesia':
                return 'kupnes_checklist_media';
            default:
                return 'fobi_checklist_media';
        }
    }
} 