<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;
    protected $fillable = [
        'observation_id',
        'fobi_user_id',
        'status',
        'notes',
    ];

    public function fobiUser()
    {
        return $this->belongsTo(FobiUser::class);
    }
    public function observation()
    {
        return $this->belongsTo(IdentifikasiObservasi::class);
    }
}
