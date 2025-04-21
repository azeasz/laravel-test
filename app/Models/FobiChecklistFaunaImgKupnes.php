<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FobiChecklistFaunaImgKupnes extends Model
{
    use SoftDeletes;

    protected $table = 'fobi_checklist_fauna_imgs_kupnes';

    protected $fillable = [
        'checklist_id',
        'fauna_id',
        'images',
        'status'
    ];

    public function checklist()
    {
        return $this->belongsTo(KupunesiaChecklist::class, 'checklist_id');
    }

    public function fauna()
    {
        return $this->belongsTo(KupunesiaFauna::class, 'fauna_id');
    }
}
