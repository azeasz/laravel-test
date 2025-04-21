<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'fobi_checklist_id', 'checklist_id', 'fauna_id', 'comment_id', 'suggestion_id', 'identification_id'];

    public function user()
    {
        return $this->belongsTo(FobiUser::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }

    public function suggestion()
    {
        return $this->hasMany(Suggestion::class, 'checklist_id', 'checklist_id');
    }
    public function identification()
    {
        return $this->belongsTo(Identification::class);
    }
     // Tambahkan scope untuk fauna_id
     public function scopeByFaunaId($query, $faunaId)
     {
         return $query->where('fauna_id', $faunaId);
     }
}
