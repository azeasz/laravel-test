<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaIdentification extends Model
{
    use SoftDeletes;

    protected $table = 'taxa_identifications';
    
    protected $fillable = [
        'user_id',
        'taxon_id',
        'checklist_id',
        'burnes_checklist_id',
        'kupnes_checklist_id',
        'comment',
        'is_withdrawn'
    ];

    protected $dates = ['deleted_at'];
    
    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id', 'id');
    }

    public function taxon()
    {
        return $this->belongsTo(Taxa::class, 'taxon_id', 'id');
    }
}
