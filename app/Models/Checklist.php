<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    protected $connection = 'second';
    protected $table = 'checklists';
    protected $fillable = [
        'latitude',
        'longitude',
        'id',
        'observer',
        'created_at',
    ];

    public function faunas()
    {
        return $this->belongsTo(Fauna::class, 'fauna_id', 'id');
    }
}
