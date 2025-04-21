<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KupunesiaChecklist extends Model
{
    use SoftDeletes;

    protected $connection = 'third';
    protected $table = 'checklists';

    public function user()
    {
        return $this->belongsTo(KupunesiaUser::class, 'user_id');
    }

    public function fauna()
    {
        return $this->belongsToMany(KupunesiaFauna::class, 'checklist_fauna', 'checklist_id', 'fauna_id')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function media()
    {
        return $this->hasOne(FobiChecklistFaunaImgKupnes::class, 'checklist_id');
    }
}
