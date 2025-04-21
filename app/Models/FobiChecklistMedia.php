<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FobiChecklistMedia extends Model
{
    use HasFactory;

    protected $table = 'fobi_checklist_media';

    protected $fillable = [
        'checklist_id',
        'media_type',
        'file_path',
        'spectrogram',
        'scientific_name',
        'location',
        'habitat',
        'description',
        'date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function checklist()
    {
        return $this->belongsTo(FobiChecklistTaxa::class, 'checklist_id', 'id');
    }
    public function qualityAssessment()
    {
        return $this->hasOne(MediaQualityAssessment::class, 'media_id');
    }

    public function verifications()
    {
        return $this->hasMany(MediaVerification::class, 'media_id');
    }

    public function flags()
    {
        return $this->hasMany(CommunityMediaFlag::class, 'media_id');
    }
}
