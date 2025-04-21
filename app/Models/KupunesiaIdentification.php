<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KupunesiaIdentification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kupunesia_identifications';

    protected $fillable = [
        'observation_id',
        'observation_type',
        'user_id',
        'taxon_id',
        'identification_level',
        'comment',
        'photo_path',
        'agreement_count',
        'disagreement_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke taxon
    public function taxon()
    {
        return $this->belongsTo(Taxa::class, 'taxon_id', 'kupnes_fauna_id');
    }

    // Relasi ke observation
    public function observation()
    {
        return $this->morphTo();
    }

    // Relasi ke agreements
    public function agreements()
    {
        return $this->hasMany(KupunesiaIdentificationAgreement::class, 'identification_id');
    }

    // Relasi ke disagreements
    public function disagreements()
    {
        return $this->hasMany(KupunesiaIdentificationDisagreement::class, 'identification_id');
    }

    // Accessor untuk nama identifier
    public function getIdentifierNameAttribute()
    {
        return $this->user->name;
    }

    // Accessor untuk nama ilmiah
    public function getScientificNameAttribute()
    {
        return $this->taxon->scientific_name;
    }

    // Accessor untuk nama umum
    public function getCommonNameAttribute()
    {
        return $this->taxon->cname_species;
    }

    // Method untuk menambah agreement
    public function addAgreement($userId)
    {
        $this->agreements()->firstOrCreate([
            'user_id' => $userId
        ]);

        $this->increment('agreement_count');
    }

    // Method untuk menghapus agreement
    public function removeAgreement($userId)
    {
        $agreement = $this->agreements()->where('user_id', $userId)->first();
        if ($agreement) {
            $agreement->delete();
            $this->decrement('agreement_count');
        }
    }

    // Method untuk mengecek apakah user sudah agree
    public function hasUserAgreed($userId)
    {
        return $this->agreements()->where('user_id', $userId)->exists();
    }

    // Method untuk menambah disagreement
    public function addDisagreement($userId, $comment = null)
    {
        $this->disagreements()->firstOrCreate([
            'user_id' => $userId,
            'comment' => $comment
        ]);

        $this->increment('disagreement_count');
    }

    // Method untuk menghapus disagreement
    public function removeDisagreement($userId)
    {
        $disagreement = $this->disagreements()->where('user_id', $userId)->first();
        if ($disagreement) {
           $disagreement->delete();
           $this->decrement('disagreement_count');
       }
   }
    // Method untuk mengecek apakah user sudah disagree
   public function hasUserDisagreed($userId)
   {
       return $this->disagreements()->where('user_id', $userId)->exists();
   }
    // Scope untuk identifikasi yang aktif
   public function scopeActive($query)
   {
       return $query->whereNull('deleted_at');
   }
    // Scope untuk identifikasi dengan agreement terbanyak
   public function scopeMostAgreed($query)
   {
       return $query->orderBy('agreement_count', 'desc');
   }

}
