<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridCell extends Model
{
    protected $fillable = [
        'cell_id',
        'min_lat',
        'max_lat',
        'min_lng',
        'max_lng',
        'area_name',
        'zoom_level'
    ];
}
