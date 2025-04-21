<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FobiChecklistFaunaImg extends Model
{
    use SoftDeletes;

    protected $table = 'fobi_checklist_fauna_imgs';

    protected $fillable = [
        'checklist_id',
        'fauna_id',
        'images',
        'status'
    ];

    public function checklist()
    {
        return $this->belongsTo(BurungnesiaChecklist::class, 'checklist_id');
    }

    public function fauna()
    {
        return $this->belongsTo(BurungnesiaFauna::class, 'fauna_id');
    }
}
