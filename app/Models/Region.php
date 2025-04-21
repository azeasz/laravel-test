<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Relasi dengan Overseer
    public function overseers()
    {
        return $this->hasMany(Overseer::class);
    }
}
