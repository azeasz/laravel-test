<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistAka extends Model
{
    protected $connection = 'second'; // Koneksi ke database pertama
    protected $table = 'checklists';

    // Tambahkan atribut source
    protected $appends = ['source'];

    public function getSourceAttribute()
    {
        return 'burungnesia';
    }
    public function genusFauna()
    {
        return $this->belongsTo(GenusFauna::class, 'fauna_id', 'fauna_id');
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
