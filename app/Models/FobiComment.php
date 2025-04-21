<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FobiComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'checklist_taxa_id',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }

    public function checklistTaxa()
    {
        return $this->belongsTo(FobiChecklistTaxa::class, 'checklist_taxa_id');
    }
}
