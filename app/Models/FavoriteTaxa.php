<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteTaxa extends Model
{
    use HasFactory;

    protected $fillable = ['fobi_user_id', 'taxa'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fobiUser()
    {
        return $this->belongsTo(FobiUser::class);
    }
}
