<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    /**
     * Get identification history for logged in user
     */
    public function getUserIdentificationHistory(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $perPage = $request->input('per_page', 10);

            $query = DB::table('taxa_identification_histories as h')
                ->select([
                    'h.*',
                    'fct.scientific_name as current_name',
                    'fct.id as checklist_id',
                    'u.uname as user_name'
                ])
                ->join('fobi_checklist_taxas as fct', 'h.checklist_id', '=', 'fct.id')
                ->join('fobi_users as u', 'h.user_id', '=', 'u.id')
                ->where('h.user_id', $user->id)
                ->orderBy('h.created_at', 'desc');

            // Apply filters if any
            if ($request->has('start_date')) {
                $query->whereDate('h.created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('h.created_at', '<=', $request->end_date);
            }
            if ($request->has('action_type')) {
                $query->where('h.action_type', $request->action_type);
            }

            $history = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getUserIdentificationHistory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data history'
            ], 500);
        }
    }

    /**
     * Get flags for logged in user
     */
    public function getUserFlags(Request $request)
    {
        try {
            $user = JWTAuth::user();
            $perPage = $request->per_page ?? 10;

            $flags = DB::table('taxa_flags as f')
                ->select([
                    'f.*',
                    'fct.scientific_name',
                    'fct.id as checklist_id',
                    'u.uname as user_name',
                    'ru.uname as resolved_by_name'
                ])
                ->join('fobi_checklist_taxas as fct', 'f.checklist_id', '=', 'fct.id')
                ->join('fobi_users as u', 'f.user_id', '=', 'u.id')
                ->leftJoin('fobi_users as ru', 'f.resolved_by', '=', 'ru.id')
                ->where('f.user_id', $user->id)
                ->orderBy('f.created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $flags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data flags'
            ], 500);
        }
    }

    /**
     * Get all identification history (admin only)
     */
    public function getAllIdentificationHistory(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $query = DB::table('taxa_identification_histories as h')
                ->select([
                    'h.*',
                    'fct.scientific_name as current_name',
                    'u.uname as user_name'
                ])
                ->join('fobi_checklist_taxas as fct', 'h.checklist_id', '=', 'fct.id')
                ->join('fobi_users as u', 'h.user_id', '=', 'u.id')
                ->orderBy('h.created_at', 'desc');

            // Apply filters
            if ($request->has('start_date')) {
                $query->whereDate('h.created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('h.created_at', '<=', $request->end_date);
            }
            if ($request->has('action_type')) {
                $query->where('h.action_type', $request->action_type);
            }

            $history = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getAllIdentificationHistory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data history'
            ], 500);
        }
    }

    /**
     * Get all flags (admin only)
     */
    public function getAllFlags(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 10;

            $query = DB::table('taxa_flags as f')
                ->select([
                    'f.*',
                    'fct.common_name',
                    'fct.scientific_name',
                    'u.uname as user_name',
                    'ru.uname as resolved_by_name'
                ])
                ->join('fobi_checklist_taxas as fct', 'f.checklist_id', '=', 'fct.id')
                ->join('fobi_users as u', 'f.user_id', '=', 'u.id')
                ->leftJoin('fobi_users as ru', 'f.resolved_by', '=', 'ru.id')
                ->orderBy('f.created_at', 'desc');

            // Filter berdasarkan status resolved
            if ($request->has('is_resolved')) {
                $query->where('f.is_resolved', $request->is_resolved === 'true');
            }

            // Filter berdasarkan flag_type
            if ($request->has('flag_type')) {
                $query->where('f.flag_type', $request->flag_type);
            }

            // Filter berdasarkan tanggal
            if ($request->has('start_date')) {
                $query->whereDate('f.created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('f.created_at', '<=', $request->end_date);
            }

            $flags = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $flags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data flags'
            ], 500);
        }
    }

    /**
     * Get identification history for specific checklist
     */
    public function getChecklistIdentificationHistory($checklistId)
    {
        try {
            $history = DB::table('taxa_identification_histories as h')
                ->select([
                    'h.*',
                    'u.uname as user_name'
                ])
                ->join('fobi_users as u', 'h.user_id', '=', 'u.id')
                ->where('h.checklist_id', $checklistId)
                ->orderBy('h.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data history'
            ], 500);
        }
    }

    /**
     * Get flags for specific checklist
     */
    public function getChecklistFlags($checklistId)
    {
        try {
            $flags = DB::table('taxa_flags as f')
                ->select([
                    'f.*',
                    'u.uname as user_name',
                    'ru.uname as resolved_by_name'
                ])
                ->join('fobi_users as u', 'f.user_id', '=', 'u.id')
                ->leftJoin('fobi_users as ru', 'f.resolved_by', '=', 'ru.id')
                ->where('f.checklist_id', $checklistId)
                ->orderBy('f.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $flags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data flags'
            ], 500);
        }
    }
}
