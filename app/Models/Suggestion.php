<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = ['checklist_id', 'fauna_id', 'user_id', 'suggested_name', 'description', 'is_cancelled'];
    public function user()
    {
        return $this->belongsTo(FobiUser::class, 'user_id');
    }
}
