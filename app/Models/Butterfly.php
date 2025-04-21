<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Butterfly extends Model
{
    use HasFactory;

    protected $fillable = [
        'species',
        'count',
        'notes',
        'observation_id',
    ];

    public function uploads()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }
    public function observation()
    {
        return $this->belongsTo(Observation::class);
    }
}
