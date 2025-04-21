<?php

namespace App\Services\Identification;

use Illuminate\Support\Facades\DB;

class ButterflyIdentificationService implements IdentificationServiceInterface
{
    public function getObservation($id)
    {
        return DB::table('fobi_checklists_kupnes')
            ->join('fobi_checklist_faunasv1', 'fobi_checklists_kupnes.fauna_id', '=', 'fobi_checklist_faunasv1.fauna_id')
            ->where('fobi_checklists_kupnes.id', $id)
            ->first();
    }

    public function addIdentification($checklistId, array $data)
    {
        return DB::table('butterfly_identifications')->insertGetId([
            'checklist_id' => $checklistId,
            'fauna_id' => $data['fauna_id'],
            'user_id' => auth()->id(),
            'comment' => $data['comment'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // ... implementasi method lainnya ...
}
