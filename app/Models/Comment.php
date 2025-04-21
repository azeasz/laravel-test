<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'fauna_id', 'checklist_id', 'content'];

    public function user()
    {
        return $this->belongsTo(FobiUser::class);
    }

    public function fauna()
    {
        return $this->belongsTo(Fauna::class);
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
