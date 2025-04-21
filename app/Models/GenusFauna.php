<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist;
use App\Models\ChecklistFauna;

class GenusFauna extends Model
{
    use HasFactory;

    protected $table = 'genus_faunas';
    protected $fillable = ['genus', 'nameLat', 'nameId', 'description', 'image', 'status'];

    public function fauna()
    {
        return $this->hasMany(Fauna::class, 'genus', 'genus');
    }

    public function checklists()
    {
        return $this->hasManyThrough(
            Checklist::class,
            ChecklistFauna::class,
            'fauna_id', // Foreign key on ChecklistFauna table
            'id', // Foreign key on Checklists table
            'fauna_id', // Local key on GenusFauna table
            'checklist_id' // Local key on ChecklistFauna table
        );
    }
}
