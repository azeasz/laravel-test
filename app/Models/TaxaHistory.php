<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxaHistory extends Model
{
    protected $table = 'taxa_histories';
    public $timestamps = false;

    protected $fillable = [
        'taxa_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_at',
        'changed_by'
    ];

    public function taxa()
    {
        return $this->belongsTo(Taxa::class, 'taxa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
