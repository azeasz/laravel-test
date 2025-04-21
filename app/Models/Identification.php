<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identification extends Model
{
    use HasFactory;

    protected $fillable = ['checklist_id', 'user_id', 'identification'];

    protected $casts = [
        'identification' => 'boolean',
    ];

    public function fobiUser()
    {
        return $this->belongsTo(FobiUser::class, 'user_id', 'id', 'uname');
    }

}
