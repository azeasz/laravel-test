<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistFauna extends Model
{
    use HasFactory;
    protected $connection = 'second';
    protected $table = 'checklist_fauna';
    protected $fillable = ['checklist_id', 'fauna_id'];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

}
