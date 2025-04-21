<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxa extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'class_key' => 'decimal:1',
        'order_key' => 'decimal:1',
        'family_key' => 'decimal:1',
        'genus_key' => 'decimal:1',
        'species_key' => 'decimal:1'
    ];

    public function media()
    {
        return $this->hasMany(TaxaMedia::class);
    }

    public function history()
    {
        return $this->hasMany(TaxaHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
