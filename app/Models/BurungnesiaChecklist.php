<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BurungnesiaChecklist extends Model
{
    use SoftDeletes;

    protected $connection = 'second';
    protected $table = 'checklists';

    public function user()
    {
        return $this->belongsTo(BurungnesiaUser::class, 'user_id');
    }

    public function fauna()
    {
        return $this->belongsToMany(BurungnesiaFauna::class, 'checklist_fauna', 'checklist_id', 'fauna_id')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function media()
    {
        return $this->hasOne(FobiChecklistFaunaImg::class, 'checklist_id');
    }
}
