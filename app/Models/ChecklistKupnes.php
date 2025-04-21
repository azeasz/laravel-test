<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistKupnes extends Model
{
    protected $connection = 'third'; // Koneksi ke database kedua
    protected $table = 'checklists';

    // Tambahkan atribut source
    protected $appends = ['source'];

    public function getSourceAttribute()
    {
        return 'kupunesia';
    }
    public function fauna()
    {
        return $this->belongsTo(Fauna::class, 'fauna_id', 'id');
    }
    public function fobiUploads()
    {
        return $this->hasMany(FobiUpload::class, 'checklist_id', 'id');
    }

}
