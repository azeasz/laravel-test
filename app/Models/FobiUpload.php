<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FobiUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'location', 'date', 'time_start', 'time_end', 'activity', 'habitat', 'other_observers', 'description', 'scientific_name', 'media_path', 'source', 'is_identified'
    ];
    public function checklist()
   {
       return $this->belongsTo(Checklist::class);
   }
}
