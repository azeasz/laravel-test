<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdministrativeArea extends Model
{
    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'bounds_north',
        'bounds_south',
        'bounds_east',
        'bounds_west'
    ];

    public function parent()
    {
        return $this->belongsTo(AdministrativeArea::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AdministrativeArea::class, 'parent_id');
    }
}
