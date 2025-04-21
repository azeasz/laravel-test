<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentifikasiObservasi extends Model
{
    use HasFactory;
    protected $fillable = [
        'species_name',
        'common_name',
        'description',
        'rating',
        'ratings_count',
        'source',
    ];
    protected $casts = [
        'observed_at' => 'date',
        'uploaded_at' => 'date',
    ];

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
    public function approvedBy()
    {
        return $this->approvals()->where('status', 'approved')->with('user');
    }

    public function rejectedBy()
    {
        return $this->approvals()->where('status', 'rejected')->with('user');
    }
}
