<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BurungnesiaIdentification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'observation_id',
        'user_id',
        'taxon_id',
        'identification_level',
        'notes',
        'agreement_count',
        'is_valid'
    ];

    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }

    public function observation()
    {
        return $this->belongsTo(FobiChecklist::class, 'observation_id');
    }

    public function taxon()
    {
        return $this->belongsTo(Fauna::class, 'taxon_id');
    }
}
