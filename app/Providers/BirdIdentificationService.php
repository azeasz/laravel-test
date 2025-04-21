<?php

namespace App\Services\Identification;

use Illuminate\Support\Facades\DB;

class BirdIdentificationService implements IdentificationServiceInterface
{
    public function getObservation($id)
    {
        return DB::table('fobi_checklists')
            ->join('fobi_checklist_faunasv1', 'fobi_checklists.id', '=', 'fobi_checklist_faunasv1.checklist_id')
            ->where('fobi_checklists.id', $id)
            ->first();
    }

    public function addIdentification($checklistId, array $data)
    {
        return DB::table('bird_identifications')->insertGetId([
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
