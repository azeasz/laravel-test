<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFauna extends Model
{
    use HasFactory;
    protected $connection = 'second';
    protected $table = 'order_faunas';
    protected $fillable = [
        'ordo_order',
        'ordo',
        'famili_order',
        'famili',
        'iucn',
    ];
    public function faunas()
    {
        return $this->belongsTo(Fauna::class);
    }

}
