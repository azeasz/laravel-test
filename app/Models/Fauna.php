<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fauna extends Model
{
    use HasFactory;

    public function checklists()
    {
        return $this->belongsToMany(Checklist::class, 'checklist_fauna', 'fauna_id', 'checklist_id')
                    ->withPivot('count', 'notes', 'breeding', 'breeding_type_id', 'breeding_note')
                    ->withTimestamps();
    }
    public function genusFauna()
    {
        return $this->belongsTo(GenusFauna::class, 'genus', 'genus');
    }


}
