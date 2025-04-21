<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'date',
        'time_start',
        'time_end',
        'complete_checklist',
        'habitat',
        'other_observers',
        'description',
        'type',
        'activity',
        'media',
        'source',
        'is_identified',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function birds()
    {
        return $this->hasMany(Bird::class);
    }

    public function butterflies()
    {
        return $this->hasMany(Butterfly::class);
    }
    public function fauna()
    {
        return $this->belongsTo(Fauna::class, 'fauna_id'); // Sesuaikan dengan nama kolom yang benar
    }
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
}
