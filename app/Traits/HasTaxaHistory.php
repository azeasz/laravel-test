<?php

namespace App\Traits;

use App\Models\TaxaHistory;
use Illuminate\Support\Facades\Auth;

trait HasTaxaHistory
{
    protected static function bootHasTaxaHistory()
    {
        static::updated(function ($model) {
            $changes = $model->getDirty();
            foreach ($changes as $field => $newValue) {
                TaxaHistory::create([
                    'taxa_id' => $model->id,
                    'field_name' => $field,
                    'old_value' => $model->getOriginal($field),
                    'new_value' => $newValue,
                    'changed_at' => now(),
                    'changed_by' => Auth::id()
                ]);
            }
        });
    }
}
